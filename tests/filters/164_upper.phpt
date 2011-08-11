--TEST--
A generated testfile for the "upper" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => 'Mixed case input')));
foreach ($items as $i) echo $i->input->upper();
--EXPECT--
MIXED CASE INPUT