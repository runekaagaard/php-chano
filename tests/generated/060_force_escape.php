<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => '<some html & special characters > here ĐÅ€£')));
foreach ($items as $i) echo $i->input->force_escape();
