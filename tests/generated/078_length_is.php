<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'a')));
foreach ($items as $i) echo $i->input->length_is(10);
