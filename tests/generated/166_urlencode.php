<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'françois & jill')));
foreach ($items as $i) echo $i->input->urlencode();
