<?php

// @author Jason Smith
// @version 2.1
// @desc Object and methods for display of a static article

// @object Article
// @desc Data structure and methods to read and display external xml, representing article content
// -----------------------------------------------
class Article {
  var $title;
  var $content;
  var $compass;
  var $css; // custom css

// @name Article
// @desc Constructor reads given xml file into local object array structures
// @param $filename xml filename (relative path where applicable)
// @param $alt flag for alternate view (for unindexed articles), default to false
// -----------------------------------------------
  function Article($filename, $title) {
    $data = implode("", file($filename));
    $parser = xml_parser_create();
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, $data, $values, $tags);
    xml_parser_free($parser);

    foreach ($values as $k => $v) {
      if ($v['type'] == 'complete' && $v['tag'] == 'content') {
        if (isset($v['attributes'])) $this->css = $v['attributes']['css'];
        $this->content = $v['value'];

      }
    }

    $this->compass = explode('/', $_SERVER['PHP_SELF']);
    array_pop($this->compass);
    $this->compass = array_pop($this->compass);
    $this->title = $title;
  }

// @name print_all
// @desc Determine title and associated tags, then print content of article (complete page)
// -----------------------------------------------
  function print_all($bann = true) {

    if ($this->css)
      print_head($this->title, false, $this->css.'.css', '..', $bann);
    else
      print_head($this->title, false, false, '..', $bann);

    print $this->content;

    print_foot('..');
  }

}

?>

