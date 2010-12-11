--TEST--
A generated testfile for the "unordered_list" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => array (
  0 => 'ulitem-a',
  1 => 'ulitem-b',
))));
foreach ($items as $i) echo $i->input->unordered_list();
--EXPECT--
	<li>ulitem-a</li>
	<li>ulitem-b</li>