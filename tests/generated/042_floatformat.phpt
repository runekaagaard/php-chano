--TEST--
A generated testfile for the "floatformat" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 0)));
foreach ($items as $i) echo $i->input->floatformat();
--EXPECT--
0