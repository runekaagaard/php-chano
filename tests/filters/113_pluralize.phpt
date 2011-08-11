--TEST--
A generated testfile for the "pluralize" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => 2)));
foreach ($items as $i) echo $i->input->pluralize();
--EXPECT--
s