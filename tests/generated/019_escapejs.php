<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'paragraph separator:
and line separator:
')));
foreach ($items as $i) echo $i->input->escapejs();
