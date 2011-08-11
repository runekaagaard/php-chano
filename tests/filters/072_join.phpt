--TEST--
A generated testfile for the "join" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => array (
  0 => 0,
  1 => 1,
  2 => 2,
))));
foreach ($items as $i) echo $i->input->join('glue');
--EXPECT--
0glue1glue2