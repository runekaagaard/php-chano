<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'A sentence with a few words in it')));
foreach ($items as $i) echo $i->input->truncatewords('not a number');
