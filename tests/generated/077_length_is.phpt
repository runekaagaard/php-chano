--TEST--
A generated testfile for the "length_is" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'a')));
foreach ($items as $i) echo $i->input->length_is(1);
--EXPECT--
1