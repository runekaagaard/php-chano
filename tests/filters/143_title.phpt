--TEST--
A generated testfile for the "title" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'discothèque')));
foreach ($items as $i) echo $i->input->title();
--EXPECT--
Discothèque