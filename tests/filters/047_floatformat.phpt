--TEST--
A generated testfile for the "floatformat" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 13.1031)));
foreach ($items as $i) echo $i->input->floatformat(-3);
--EXPECT--
13.103