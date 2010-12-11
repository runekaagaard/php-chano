<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'oneword')));
foreach ($items as $i) echo $i->input->wordcount();
