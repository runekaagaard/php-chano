--TEST--
A generated testfile for the "pluralize" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 1)));
foreach ($items as $i) echo $i->input->pluralize('es');
--EXPECT--
