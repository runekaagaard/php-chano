--TEST--
A generated testfile for the "unorderedlist" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => array (
  0 => 'item 1',
  1 => 
  array (
  ),
))));
foreach ($items as $i) echo $i->input->unorderedlist();
--EXPECT--
	<li>item 1</li>