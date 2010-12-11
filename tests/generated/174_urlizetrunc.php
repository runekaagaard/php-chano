<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'http://short.com/')));
foreach ($items as $i) echo $i->input->urlizetrunc(20);
