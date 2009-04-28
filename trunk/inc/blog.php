<?php

// @author Jason Smith
// @version 2.9
// @desc Object and methods for interpretation & display of RSS in blog/archive/tag cloud

// @object Blog
// @desc Data structure and methods to read and display external xml (RSS 2.0 compliant), representing news & archive
// -----------------------------------------------
class Blog {
  var $news;
  var $tags; // Tags object

// @name Blog
// @desc Constructor reads given xml file into local object array structures
// @param $rss xml filename for rss (relative path where applicable)
// @param $tags xml filename for tags (relative path where applicable)
// -----------------------------------------------
  function Blog($rss, $tags) {
    $this->tags = new Tags($tags);
    
    $data = implode("", file($rss));
    $parser = xml_parser_create();
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, $data, $values, $sets);
    xml_parser_free($parser);

    foreach ($values as $k => $v) {
      if  ($v['type'] == 'complete') {
        if ($v['tag'] == 'title') {
          $this->news[]['heading'] = $v['value'];
          foreach ($this->tags->news as $x => $y)
            if ($v['value'] == $y['title'])
              $this->news[count($this->news)-1]['tags'] = $y['tags'];
        }
        if ($v['tag'] == 'description')
          $this->news[count($this->news)-1]['content'] = $v['value'];
        if ($v['tag'] == 'pubDate')
          $this->news[count($this->news)-1]['timestamp'] = $v['value'];
      }
    }

    array_shift($this->news);
  }

// @name print_recent
// @desc Display latest entries from news structure (complete page)
// @param $entries number of entries to print
// -----------------------------------------------
  function print_recent($entries) {
    print_head('The Latest', true, false, '.');
//    print_a($this->news);

    print<<<EOT
<div id="blog">
EOT;

    if ($entries > count($this->news)) $entries = count($this->news);
    for ($i=0; $i<$entries; $i++) {
      print '
<h2><a href="Archive/Title/' . $this->translate_title($this->news[$i]['heading']) . '">' . $this->news[$i]['heading'] . '</a></h2>
';
//      if (strtotime($this->news[$i]['timestamp']) > strtotime("-1 day"))
//        print '<img class="new" src="stamp.gif" alt="New" />';
      print $this->news[$i]['content'] . '
<p class="timestamp">Posted <a href="Archive/Date/' . strftime('%d-%b-%Y', strtotime($this->news[$i]['timestamp']))
 . '">' . strftime('%d %b %Y', strtotime($this->news[$i]['timestamp'])) . '</a>, tagged with ';
 
      $this->tags->print_tags($this->translate_title($this->news[$i]['heading']), ' ', ' ', '.');
      print '</p>
';
    }

    print_foot('.');
  }

// @name print_date
// @desc Display all entries posted on given date (complete page)
// @param $date
// -----------------------------------------------
  function print_date($date) {
    print_head(str_replace("-", " ", $date), false, false, '../..');

    print<<<EOT
<div id="blog">
EOT;

    for ($i=0; $i<count($this->news); $i++) {
      if (strtotime(strftime('%d %b %Y', strtotime($this->news[$i]['timestamp']))) ==
        strtotime($date)) {
        print '
<h2><a href="../Title/' . $this->translate_title($this->news[$i]['heading']) . '">' . $this->news[$i]['heading'] . '</a></h2>
' . $this->news[$i]['content'] . '
<p class="timestamp">Posted <a href="../Date/' . strftime('%d-%b-%Y', strtotime($this->news[$i]['timestamp']))
 . '">' . strftime('%d %b %Y', strtotime($this->news[$i]['timestamp'])) . '</a>, tagged with ';
 
      $this->tags->print_tags($this->translate_title($this->news[$i]['heading']), ' ', false, '../..');
      print '</p>
';
      }
    }

    print_foot('../..');
  }

// @name print_title
// @desc Display entry with given title (complete page)
// @param $title
// -----------------------------------------------
  function print_title($title) {
    for ($i=0; $i<count($this->news); $i++) {
      if ($this->translate_title($title) == $this->translate_title($this->news[$i]['heading'])) {
        print_head($this->news[$i]['heading'], false, false, '../..');
        print '
<div id="blog">
<h2><a href="' . $this->translate_title($this->news[$i]['heading']) . '">' . $this->news[$i]['heading'] . '</a></h2>
' . $this->news[$i]['content'] . '
<p class="timestamp">Posted <a href="../Date/' . strftime('%d-%b-%Y', strtotime($this->news[$i]['timestamp']))
 . '">' . strftime('%d %b %Y', strtotime($this->news[$i]['timestamp'])) . '</a>, tagged with ';
 
      $this->tags->print_tags($this->translate_title($this->news[$i]['heading']), ' ', false, '../..');
      print '</p>
';
      }
    }

    print_foot('../..');
  }

// @name translate_title
// @desc Replace all questionable characters with _ for compliant path translation
// @param $str string
// @return translated string
// -----------------------------------------------
  function translate_title($str) {
    $str = str_replace(" ", "_", $str);
    $str = str_replace("?", "_", $str);
    $str = str_replace(",", "_", $str);
    $str = str_replace(".", "_", $str);
    $str = str_replace(":", "_", $str);
    $str = str_replace("(", "_", $str);
    $str = str_replace(")", "_", $str);
    $str = str_replace("&", "_", $str);
    $str = str_replace("=", "_", $str);
    $str = str_replace("'", "_", $str);
    return $str;
  }
  
// @name print_all
// @desc Display archive entry point, all available dates (complete page)
// @todo Display article titles
// -----------------------------------------------
  function print_archive() {
    print_head('Archive', false, false, '..');

    print '<h2>By Date</h2>
<ul>';

    for ($i=0; $i<count($this->news); $i++)
      $arr[$i] = $this->news[$i]['timestamp'];
    natsort($arr);

    $temp=0;

    for ($i=0; $i<count($arr); $i++) {
      if ($temp != strftime('%d %b %Y', strtotime($arr[$i]))) {

        print '<li><a href="Date/' . strftime('%d-%b-%Y', strtotime($arr[$i])) . '">' . strftime('%d %b %Y', strtotime($arr[$i])) . '</a></li>
';
        $temp = strftime('%d %b %Y', strtotime($arr[$i]));
      }
    }

    print '</ul>';

    print '<p>'.count($this->news).' articles.';

    print_foot('..');
  }

  function length() {
    return $this->news.length();
  }

  function print_index() {
    print_head('Index', false, false, '..');

    print '
<h2>By Title</h2>
<ul>';

    for ($i=0; $i<count($this->news); $i++)
      $arr[$i] = $this->news[$i]['heading'];

    sort($arr);

    for ($i=0; $i<count($arr); $i++) {
      print '<li><a href="../Archive/Title/' . $this->translate_title($arr[$i]) . '">' . $arr[$i] . '</a></li>
';
    }

    print '</ul><div>';

    print_foot('..');
  }

// @name print_rand
// @desc Display n random entries from news structure (complete page)
// @param $entries number of entries to print
// -----------------------------------------------
  function print_rand($entries) {
    print_head('Oh yeah', true, false, '..');
//    print_a($this->news);
    print<<<EOT
<div id="blog">
EOT;
    if ($entries > count($this->news)) $entries = count($this->news);

    shuffle($this->news);

    for ($i=0; $i<$entries; $i++) {
      print '<h2><a href="Archive/Title/' . $this->translate_title($this->news[$i]['heading']) . '">' . $this->news[$i]['heading'] . '</a></h2>';
//      if (strtotime($this->news[$i]['timestamp']) > strtotime("-1 day"))
//        print '<img class="new" src="stamp.gif" alt="New" />';
      print $this->news[$i]['content'] . '<p class="timestamp">Posted <a href="Archive/Date/' . strftime('%d-%b-%Y', strtotime($this->news[$i]['timestamp'])) . '">' . strftime('%d %b %Y', strtotime($this->news[$i]['timestamp'])) . '</a>, tagged with ';
      $this->tags->print_tags($this->translate_title($this->news[$i]['heading']), ' ', ' ', '.');
      print '</p>';
    }
    print_foot('..');
  }

}


?>

