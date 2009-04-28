<?php

// @author Jason Smith
// @version 3.1
// @desc Directory index for article

require('../inc/util.php');
$art = new Article('book.xml', 'XML & HTML Character Entity Reference');
$art->print_all();

?>
