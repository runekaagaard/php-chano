--TEST--
A generated testfile for the "upper" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 'Mixed case input')));
foreach ($items as $i) echo $i->input->upper();
--EXPECT--
MIXED CASE INPUT