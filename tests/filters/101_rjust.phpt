--TEST--
A generated testfile for the "rjust" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => '123')));
foreach ($items as $i) echo $i->input->rjust(4);
--EXPECT--
 123