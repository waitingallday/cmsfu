<?php

// @author Jason Smith
// @version 3.1
// @desc Revised object for tag logic & display of tag cloud

// @object Tags
// @desc Data structure and methods to read and display external xml, representing article details and structure
// -----------------------------------------------
class Tags {
  var $news;

  var $tags;
  var $tagIndex;

// @name Tags
// @desc Constructor reads given xml file into local object array structures
// @param $filename xml filename (relative path where applicable)
// -----------------------------------------------
  function Tags($filename) {
    $data = implode("", file($filename));
    $parser = xml_parser_create();
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, $data, $values, $tags);
    xml_parser_free($parser);
    $this->tags[] = 0;
    $this->tagIndex[] = 0;

    foreach ($values as $k => $v) {
      if ($v['type'] == 'open') {
        if ($v['tag'] == 'news') $this->news[]['title'] = $v['attributes']['title'];

      } elseif ($v['type'] == 'complete' && $v['tag'] == 'tag') {
        $a = count($this->news)-1;
        $this->news[$a]['tags'][] = $v['value'];

        if (!in_array($v['value'], $this->tags, true)) {
          $this->tags[] = $v['value'];
          $this->tagIndex[] = 1;
        } else
          $this->tagIndex[array_search($v['value'], $this->tags, true)]++;

      }
    }

    array_shift($this->tags);
    array_shift($this->tagIndex);
  }

// @name replace_xml
// @desc Replace normal string containing & with xml entity
// @param $in string to replace
// @return corrected string
// -----------------------------------------------
  function replace_xml($in) {
    return str_replace("&", "&amp;", $in);
  }

// @name translate_title
// @desc Replace all questionable characters with _ for path translation
// @param $ref reference to article array element
// @return translated string
// -----------------------------------------------
  function translate_title($ref) {
    $str = $this->news[$ref]['title'];
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

// @name get_title
// @desc Traverse article array and translate all titles to compare with search value
// @param $needle search value
// @return article title
// -----------------------------------------------
  function get_title($needle) {
    foreach($this->article as $k => $v)
      $arr[] = $this->translate_title($k);
    return $this->article[array_search($needle, $arr, strict)]['title'];
  }

// @name match_title
// @desc Traverse article array and translate all titles to compare with search value
// @param $needle search value
// @return article index
// -----------------------------------------------
  function match_title($needle) {
    foreach($this->news as $k => $v)
      $arr[] = $this->translate_title($k);
    return (array_search($needle, $arr, true));
  }

// @name print_tags
// @desc Print list of tags relating to this article
// @param $compass article title
// @param $sep separator string
// @param $except don't print this tag
// -----------------------------------------------
  function print_tags($compass, $sep, $except = ' ', $rel = '..') {
    foreach ($this->news[$this->match_title($compass)]['tags'] as $k => $v)
      if ($v != $except) $arr[] = $v;

    foreach ($arr as $k => $v) {
      print '<a href="' . $rel . '/tags/' . $v . '">' . $v . '</a>';
      if ($k != count($arr)-1)
         print $sep;
      }
  }

// @name print_cloud
// @desc Display formatted tag cloud (complete page)
// @param $sort flag to determine sort - true for alphabetic, false for frequency
// -----------------------------------------------
  function print_cloud($sort = true) {
    print_head('Tag Cloud', false, false, '..');
    print '<h2>Tag Cloud</h2><b class="irtop"><b class="r1"></b><b class="r2"></b><b class="r3"></b><b class="r4"></b></b>
	<div id="tags"><p>';

    $arr = $this->tags;

    if ($sort)
      natcasesort($arr);

    foreach ($arr as $k => $v)
      print '<a href="' . $v . '" class="t' . $this->tag_size($this->tagIndex[$k]) . '">' . $v . '</a> ';

    print '</p></div>
<b class="irbottom"><b class="r4"></b><b class="r3"></b><b class="r2"></b><b class="r1"></b></b>
	';
    print_foot('..');
  }

  function print_cloud_s($sort = true, $rel = '.') {
    print '<div id="tags">';
    $arr = $this->tags;

    if ($sort)
      natcasesort($arr);

    foreach ($arr as $k => $v)
      print '<a href="'. $rel .'/tags/' . $v . '" class="t' . $this->tag_size($this->tagIndex[$k]) . '">' . $v . '</a> ';

    print '</div>
        ';
  }


// @name tag_size
// @desc Determine font class size from tag frequency (upper limit: 5)
// @param $in frequency of tag
// @return font size
// -----------------------------------------------
  function tag_size($in) {
    return ($in < 7) ?$in:7;
  }

// @name print_tag
// @desc Display all articles for given tag (complete page)
// @param $tag
// -----------------------------------------------
  function print_tag($tag) {
    $tag = str_replace("_", ".", $tag);
    print_head($tag, false, false, '..');
    print '<h2>' . $tag . '</h2>
<ul>';
    foreach ($this->news as $k => $v)
      for ($i=0; $i<count($v['tags']); $i++)
        if ($v['tags'][$i] == $tag) {
          print '<li><a href="../Archive/Title/' . $this->translate_title($k) . '">' .
            $v['title'] . '</a>';
          if (count($v['tags']) > 1) {
            print '&nbsp;&nbsp;<span class="small">(other tags: ';
            print $this->print_tags($this->translate_title($k), ', ', $tag, '..');
            print ')</span>';
          }
          print '</li>';
        }

    print '</ul>';
//    print_a($this->article);
//    print '<p>Search through article array and print title &amp; ref to article</p>';

    print_foot('..');

  }

  function length() {
    print count($this->news);
  }

  function print_count() {
    print '<p>This site is a lasting tribute to short term memory and the number ' . count($this->news) . '. It requires no feedback from you.</p>
<p>Thank you.</p>';
  }
}

?>

