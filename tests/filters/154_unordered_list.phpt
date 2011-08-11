--TEST--
A generated testfile for the "unorderedlist" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => array (
  0 => 'item 1',
  1 => 'item 2',
))));
foreach ($items as $i) echo $i->input->unorderedlist();
--EXPECT--
	<li>item 1</li>
	<li>item 2</li>