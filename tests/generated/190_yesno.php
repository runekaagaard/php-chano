<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => true)));
foreach ($items as $i) echo $i->input->yesno('certainly,get out of town,perhaps');
