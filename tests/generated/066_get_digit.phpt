--TEST--
A generated testfile for the "get_digit" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'xyz')));
foreach ($items as $i) echo $i->input->get_digit(0);
--EXPECT--
xyz