--TEST--
A generated testfile for the "slice_" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 'æøcdefg')));
foreach ($items as $i) echo $i->input->slice('0::2');
--EXPECT--
æceg
