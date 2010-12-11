<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => '1135810800')));
foreach ($items as $i) echo $i->input->date('jS o\\f F');
