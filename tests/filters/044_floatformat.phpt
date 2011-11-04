--TEST--
A generated testfile for the "floatformat" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 6)));
foreach ($items as $i) echo $i->input->floatformat(3);
--EXPECT--
6.000