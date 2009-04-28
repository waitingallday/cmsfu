<?php

// @author Jason Smith
// @version 2.4
// @desc Main PHP include and helper functions

require('side.php');
require('blog.php');
require('tags.php');
require('article.php');
//require('pool.php');

// @name print_a
// @desc print an array in <pre> for display to web
// @param $arr array
// -----------------------------------------------
function print_a($arr) {
  print("<pre>");
  print_r($arr);
  print("</pre>");
}

// @name print_head
// @desc print a standard header and right nav
// @param $title caption to place inside <title> and sub on main page
// @param $rss flag to include RSS, default to false
// @param $css use different CSS to root, default to false
// @param $rel relationship to root
// -----------------------------------------------
function print_head($title, $rss = false, $css = false, $rel, $bann = true) {
  $headTitle = 'dev_notes';
  if ($title) $headTitle .= ' # ' . $title;

  if ($css) $cssrel = '.';
  else {
    $cssrel = $rel;
    $css = 'white.css';
  }

  require_once("$rel/inc/header.php");
}

// @name print_foot
// @desc Print a standard footer
// -----------------------------------------------
function print_foot($rel) {
  require_once("$rel/inc/footer.php");
}

?>
