--TEST--
A generated testfile for the "make_list" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'abc')));
foreach ($items as $i) echo $i->input->make_list();
--EXPECT--
Array