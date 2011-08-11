--TEST--
A generated testfile for the "floatformat" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => 13.1031)));
foreach ($items as $i) echo $i->input->floatformat(-3);
--EXPECT--
13.103