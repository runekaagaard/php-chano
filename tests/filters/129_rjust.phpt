--TEST--
A generated testfile for the "rjust" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => 'test')));
foreach ($items as $i) echo $i->input->rjust(3);
--EXPECT--
test