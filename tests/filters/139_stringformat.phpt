--TEST--
A generated testfile for the "stringformat" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => 1)));
foreach ($items as $i) echo $i->input->stringformat('z');
--EXPECT--
