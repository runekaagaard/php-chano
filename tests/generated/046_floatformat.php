<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 6.2)));
foreach ($items as $i) echo $i->input->floatformat(-3);
