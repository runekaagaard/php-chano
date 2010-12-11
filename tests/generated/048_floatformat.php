<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 11.1197)));
foreach ($items as $i) echo $i->input->floatformat(-2);
