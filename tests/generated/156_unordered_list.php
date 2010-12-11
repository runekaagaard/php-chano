<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => array (
  0 => 'item 1',
  1 => 
  array (
    0 => 'item 1.1',
    1 => 'item1.2',
  ),
  2 => 'item 2',
))));
foreach ($items as $i) echo $i->input->unordered_list();
