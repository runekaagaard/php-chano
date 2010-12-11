<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 1.1258999068426E+15)));
foreach ($items as $i) echo $i->input->filesizeformat();
