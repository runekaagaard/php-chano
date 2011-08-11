--TEST--
A generated testfile for the "floatformat" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => 6.2)));
foreach ($items as $i) echo $i->input->floatformat(-3);
--EXPECT--
6.200