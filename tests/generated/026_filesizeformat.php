<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 1073741823)));
foreach ($items as $i) echo $i->input->filesizeformat();
