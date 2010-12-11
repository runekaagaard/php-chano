--TEST--
A generated testfile for the "unordered_list" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => array (
  0 => 'States',
  1 => 
  array (
    0 => 'Kansas',
    1 => 
    array (
      0 => 'Lawrence',
      1 => 'Topeka',
    ),
    2 => 'Illinois',
  ),
))));
foreach ($items as $i) echo $i->input->unordered_list();
--EXPECT--
	<li>States
	<ul>
		<li>Kansas
		<ul>
			<li>Lawrence</li>
			<li>Topeka</li>
		</ul>
		</li>
		<li>Illinois</li>
	</ul>
	</li>