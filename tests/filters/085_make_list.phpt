--TEST--
A generated testfile for the "make_list" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => 'a火ø')));
foreach ($items as $i) var_dump($i->input->makelist()->v);
--EXPECT--
array(3) {
  [0]=>
  string(1) "a"
  [1]=>
  string(3) "火"
  [2]=>
  string(2) "ø"
}