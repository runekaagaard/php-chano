<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => false)));
foreach ($items as $i) echo $i->input->yesno();
