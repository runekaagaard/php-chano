--TEST--
A generated testfile for the "filesizeformat" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 1099511627776)));
foreach ($items as $i) echo $i->input->filesizeformat();
--EXPECT--
1.0 TB