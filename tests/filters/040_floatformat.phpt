--TEST--
A generated testfile for the "floatformat" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => 0.07)));
foreach ($items as $i) echo $i->input->floatformat();
--EXPECT--
0.1