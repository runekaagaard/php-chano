<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => array (
  0 => 1,
  1 => 2,
  2 => 3,
  3 => 4,
))));
foreach ($items as $i) echo $i->input->length();
