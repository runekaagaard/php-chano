<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'abc')));
foreach ($items as $i) echo $i->input->make_list();
