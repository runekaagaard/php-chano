--TEST--
A generated testfile for the "slice_" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'abcdefg')));
foreach ($items as $i) echo $i->input->slice('0::2');
--EXPECT--
aceg
