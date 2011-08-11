--TEST--
A generated testfile for the "pluralize" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => array (
))));
foreach ($items as $i) echo $i->input->pluralize();
--EXPECT--
