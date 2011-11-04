--TEST--
A generated testfile for the "addslashes" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 123)));
foreach ($items as $i) echo $i->input->addslashes();
--EXPECT--
123