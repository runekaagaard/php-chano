--TEST--
A generated testfile for the "make_list" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => 1234)));
foreach ($items as $i) var_dump($i->input->makelist()->v);
--EXPECT--
array(4) {
  [0]=>
  int(1)
  [1]=>
  int(2)
  [2]=>
  int(3)
  [3]=>
  int(4)
}
