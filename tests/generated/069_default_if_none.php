<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => '')));
foreach ($items as $i) echo $i->input->default_if_none('default');
