--TEST--
A generated testfile for the "wordcount" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 123)));
foreach ($items as $i) echo $i->input->wordcount();
--EXPECT--
1