--TEST--
A generated testfile for the "addslashes" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => '"double quotes" and \'single quotes\'')));
foreach ($items as $i) echo $i->input->addslashes();
--EXPECT--
\"double quotes\" and \'single quotes\'