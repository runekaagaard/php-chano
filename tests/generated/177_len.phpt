--TEST--
A generated testfile for the "len" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'http://31characteruri.com/test/')));
foreach ($items as $i) echo $i->input->len();
--EXPECT--
31