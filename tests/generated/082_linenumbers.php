<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'x
x
x
x
x
x
x
x
x
x')));
foreach ($items as $i) echo $i->input->linenumbers();
