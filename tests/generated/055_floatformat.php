<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'foo')));
foreach ($items as $i) echo $i->input->floatformat('bar');
