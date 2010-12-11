--TEST--
A generated testfile for the "divisibleby" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 4)));
foreach ($items as $i) echo $i->input->divisibleby(2);
--EXPECT--
1