<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'lots of words')));
foreach ($items as $i) echo $i->input->wordcount();
