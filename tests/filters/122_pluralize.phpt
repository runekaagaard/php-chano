--TEST--
A generated testfile for the "pluralize" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 2)));
foreach ($items as $i) echo $i->input->pluralize('y', 'ies');
--EXPECT--
ies
