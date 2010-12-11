<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 52428800)));
foreach ($items as $i) echo $i->input->filesizeformat();
