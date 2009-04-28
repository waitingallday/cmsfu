<?php

// @author Jason Smith
// @version 2.1
// @desc Directory index for display of tag cloud or RSS marked with given tag

error_reporting(0);

require('../inc/util.php');
$tags = new Tags('../index.xml');

foreach ($_GET as $k => $v);
  $p = $k;

if ($p) {
  $tags->print_tag($p);
} else {
  $tags->print_cloud();
}


?>
