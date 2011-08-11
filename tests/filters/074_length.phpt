--TEST--
A generated testfile for the "length" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => array (
  0 => 1,
  1 => 2,
  2 => 3,
  3 => 4,
))));
foreach ($items as $i) echo $i->input->length();
--EXPECT--
4