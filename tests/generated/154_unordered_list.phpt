--TEST--
A generated testfile for the "unordered_list" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => array (
  0 => 'item 1',
  1 => 'item 2',
))));
foreach ($items as $i) echo $i->input->unordered_list();
--EXPECT--
	<li>item 1</li>
	<li>item 2</li>