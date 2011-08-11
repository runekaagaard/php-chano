--TEST--
A generated testfile for the "default" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => NULL)));
foreach ($items as $i) echo $i->input->emptyor('default');
--EXPECT--
default
