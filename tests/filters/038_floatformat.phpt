--TEST--
A generated testfile for the "floatformat" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => 7)));
foreach ($items as $i) echo $i->input->floatformat();
--EXPECT--
7