<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 0.7)));
foreach ($items as $i) echo $i->input->floatformat();
