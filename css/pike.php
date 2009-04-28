<?php

// @author Jason Smith
// @version 3.2
// @desc Directory index for article

require('../inc/util.php');
$art = new Article('book.xml', 'CSS Reference');
$art->print_all();

?>
