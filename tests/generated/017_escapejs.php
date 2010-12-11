<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'and lots of whitespace: 
	')));
foreach ($items as $i) echo $i->input->escapejs();
