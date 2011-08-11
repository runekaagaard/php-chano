--TEST--
A generated testfile for the "lower" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => 'TEST')));
foreach ($items as $i) echo $i->input->lower();
--EXPECT--
test