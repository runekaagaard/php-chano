--TEST--
A generated testfile for the "getdigit" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'xyz')));
foreach ($items as $i) echo $i->input->getdigit(0);
--EXPECT--
xyz
