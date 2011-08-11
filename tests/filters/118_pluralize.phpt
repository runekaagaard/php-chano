--TEST--
A generated testfile for the "pluralize" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => 0)));
foreach ($items as $i) echo $i->input->pluralize('es');
--EXPECT--
es