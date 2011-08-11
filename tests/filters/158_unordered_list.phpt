--TEST--
A generated testfile for the "unorderedlist" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => array (
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
foreach ($items as $i) echo $i->input->unorderedlist();
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