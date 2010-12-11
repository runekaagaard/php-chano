<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => array (
  0 => 1,
  1 => 2,
  2 => 3,
))));
foreach ($items as $i) echo $i->input->pluralize();
