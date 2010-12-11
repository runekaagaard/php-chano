<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'Sør-Trøndelag')));
foreach ($items as $i) echo $i->input->iriencode();
