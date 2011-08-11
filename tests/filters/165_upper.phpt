--TEST--
A generated testfile for the "upper" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => 'ë')));
foreach ($items as $i) echo $i->input->upper();
--EXPECT--
Ë