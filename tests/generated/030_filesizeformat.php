<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 2.2517998136852E+18)));
foreach ($items as $i) echo $i->input->filesizeformat();
