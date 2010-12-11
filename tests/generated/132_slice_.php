<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'abcdefg')));
foreach ($items as $i) echo $i->input->slice_('-1');
