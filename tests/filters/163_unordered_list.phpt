--TEST--
A generated testfile for the "unorderedlist" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => array (
  0 => 'States',
  1 => 
  array (
    0 => 
    array (
      0 => 'Kansas',
      1 => 
      array (
        0 => 
        array (
          0 => 'Lawrence',
          1 => 
          array (
          ),
        ),
        1 => 
        array (
          0 => 'Topeka',
          1 => 
          array (
          ),
        ),
      ),
    ),
    1 => 
    array (
      0 => 'Illinois',
      1 => 
      array (
      ),
    ),
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