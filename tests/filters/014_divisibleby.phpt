--TEST--
A generated testfile for the "divisibleby" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => 4)));
foreach ($items as $i) echo $i->input->divisibleby(3);
--EXPECT--
