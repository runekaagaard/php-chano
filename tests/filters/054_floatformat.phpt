--TEST--
A generated testfile for the "floatformat" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 18.125)));
foreach ($items as $i) echo $i->input->floatformat(2);
--EXPECT--
18.13