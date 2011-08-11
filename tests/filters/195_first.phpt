--TEST--
A generated testfile for the "first" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => (object)array (0,1,3))));
foreach ($items as $i) echo $i->input->first();
--EXPECT--
0