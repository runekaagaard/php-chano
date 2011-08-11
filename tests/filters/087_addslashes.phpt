--TEST--
A generated testfile for the "addslashes" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => 123)));
foreach ($items as $i) echo $i->input->addslashes();
--EXPECT--
123