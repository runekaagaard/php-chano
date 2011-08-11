--TEST--
A generated testfile for the "first" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => array (0,1,3))));
foreach ($items as $i) echo $i->input->first();
--EXPECT--
0