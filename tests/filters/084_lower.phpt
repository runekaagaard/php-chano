--TEST--
A generated testfile for the "lower" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 'Ë')));
foreach ($items as $i) echo $i->input->lower();
--EXPECT--
ë