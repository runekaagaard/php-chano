--TEST--
A generated testfile for the "default" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'val')));
foreach ($items as $i) echo $i->input->emptyor('default');
--EXPECT--
val