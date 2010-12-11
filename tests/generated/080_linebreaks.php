<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'line 1
line 2')));
foreach ($items as $i) echo $i->input->linebreaks();
