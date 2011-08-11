--TEST--
A generated testfile for the "lower" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => 'Ë')));
foreach ($items as $i) echo $i->input->lower();
--EXPECT--
ë