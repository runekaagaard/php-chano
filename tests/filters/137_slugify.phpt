--TEST--
A generated testfile for the "slugify" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 'Un éléphant à l\'orée du bois')));
foreach ($items as $i) echo $i->input->slugify();
--EXPECT--
un-elephant-a-loree-du-bois