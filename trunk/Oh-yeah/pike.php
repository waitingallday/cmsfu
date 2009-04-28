<?php

// @author Jason Smith
// @version 2.2
// @desc Directory index for archived RSS

require('../inc/util.php');
$blog = new Blog('../data.xml', '../index.xml');

$blog->print_rand(4);

?>
