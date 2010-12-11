--TEST--
A generated testfile for the "unordered_list" filter.
--FILE--
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
--EXPECT--
	<li>item 1
	<ul>
		<li>item 1.1</li>
		<li>item1.2</li>
	</ul>
	</li>
	<li>item 2</li>