<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => '<some html & special characters > here')));
foreach ($items as $i) echo $i->input->force_escape();
