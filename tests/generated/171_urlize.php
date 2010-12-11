<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'djangoproject.org')));
foreach ($items as $i) echo $i->input->urlize();
