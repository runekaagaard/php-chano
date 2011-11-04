--TEST--
A generated testfile for the "pluralize" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => array (
  0 => 1,
))));
foreach ($items as $i) echo $i->input->pluralize();
--EXPECT--
