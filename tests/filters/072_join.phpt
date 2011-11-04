--TEST--
A generated testfile for the "join" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => array (
  0 => 0,
  1 => 1,
  2 => 2,
))));
foreach ($items as $i) echo $i->input->join('glue');
--EXPECT--
0glue1glue2