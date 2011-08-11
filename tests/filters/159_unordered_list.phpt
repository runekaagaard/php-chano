--TEST--
A generated testfile for the "unorderedlist" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => array (
  0 => 'ulitem-a',
  1 => 'ulitem-b',
))));
foreach ($items as $i) echo $i->input->unorderedlist();
--EXPECT--
	<li>ulitem-a</li>
	<li>ulitem-b</li>