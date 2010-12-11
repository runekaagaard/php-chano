<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => array (
  0 => 1,
))));
foreach ($items as $i) echo $i->input->pluralize();
