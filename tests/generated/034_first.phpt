--TEST--
A generated testfile for the "first" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => '')));
foreach ($items as $i) echo $i->input->first();
--EXPECT--
