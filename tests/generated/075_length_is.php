<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => array (
))));
foreach ($items as $i) echo $i->input->length_is(0);
