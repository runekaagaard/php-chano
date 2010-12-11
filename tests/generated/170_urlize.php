<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'www.google.com')));
foreach ($items as $i) echo $i->input->urlize();
