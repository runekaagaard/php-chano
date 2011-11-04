--TEST--
A generated testfile for the "unorderedlist" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => array (
  0 => 'item 1',
  1 => 
  array (
    0 => 
    array (
      0 => 'item 1.1',
      1 => 
      array (
      ),
    ),
    1 => 
    array (
      0 => 'item 1.2',
      1 => 
      array (
      ),
    ),
  ),
))));
foreach ($items as $i) echo $i->input->unorderedlist();
--EXPECT--
	<li>item 1
	<ul>
		<li>item 1.1</li>
		<li>item 1.2</li>
	</ul>
	</li>