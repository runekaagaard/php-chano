--TEST--
A generated testfile for the "pluralize" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => array (
))));
foreach ($items as $i) echo $i->input->pluralize();
--EXPECT--
s