<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'xyz')));
foreach ($items as $i) echo $i->input->get_digit(0);
