<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'Ångström was here')));
foreach ($items as $i) echo $i->input->truncatewords_html(1);
