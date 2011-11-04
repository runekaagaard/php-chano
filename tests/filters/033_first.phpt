--TEST--
A generated testfile for the "first" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => array (0,1,3))));
foreach ($items as $i) echo $i->input->first();
--EXPECT--
0