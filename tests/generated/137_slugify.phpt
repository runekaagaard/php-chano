--TEST--
A generated testfile for the "slugify" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'Un éléphant à l\'orée du bois')));
foreach ($items as $i) echo $i->input->slugify();
--EXPECT--
un-elephant-a-loree-du-bois