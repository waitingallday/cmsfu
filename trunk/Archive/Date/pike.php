<?php

// @author Jason Smith
// @version 2.2
// @desc Directory index for RSS, displayed for given date

error_reporting(0);

require('../../inc/util.php');
$blog = new Blog('../../data.xml', '../../index.xml');

foreach ($_GET as $k => $v);
  $p = $k;

$blog->print_date($p);

?>
