--TEST--
A generated testfile for the "floatformat" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 13.1031)));
foreach ($items as $i) echo $i->input->floatformat('bar');
--EXPECT--
13.1