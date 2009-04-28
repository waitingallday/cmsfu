<?php

// @author Jason Smith
// @version 4.0
// @desc Root directory index (heavily modified)

//error_reporting(0);

require('inc/util.php');
$blog = new Blog('data.xml', 'index.xml');
$blog->print_recent(5);

?>
