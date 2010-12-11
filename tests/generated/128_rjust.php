<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'test')));
foreach ($items as $i) echo $i->input->rjust(10);
