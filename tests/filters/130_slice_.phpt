--TEST--
A generated testfile for the "slice_" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 'abcdefg')));
foreach ($items as $i) echo $i->input->slice('0');
--EXPECT--
