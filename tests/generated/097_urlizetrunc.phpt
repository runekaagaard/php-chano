--TEST--
A generated testfile for the "urlizetrunc" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 123)));
foreach ($items as $i) echo $i->input->urlizetrunc(1);
--EXPECT--
123