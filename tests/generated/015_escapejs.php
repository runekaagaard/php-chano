<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => '"double quotes" and \'single quotes\'')));
foreach ($items as $i) echo $i->input->escapejs();
