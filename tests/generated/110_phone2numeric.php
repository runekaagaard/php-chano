<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => '0800 flowers')));
foreach ($items as $i) echo $i->input->phone2numeric();
