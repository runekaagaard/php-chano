--TEST--
A generated testfile for the "floatformat" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => NULL)));
foreach ($items as $i) echo $i->input->floatformat();
--EXPECT--
