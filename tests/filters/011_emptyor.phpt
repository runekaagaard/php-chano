--TEST--
A generated testfile for the "default" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => NULL)));
foreach ($items as $i) echo $i->input->emptyor('default');
--EXPECT--
default
