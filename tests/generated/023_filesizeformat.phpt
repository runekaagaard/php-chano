--TEST--
A generated testfile for the "filesizeformat" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 1048575)));
foreach ($items as $i) echo $i->input->filesizeformat();
--EXPECT--
1024.0 KB