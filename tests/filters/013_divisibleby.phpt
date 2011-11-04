--TEST--
A generated testfile for the "divisibleby" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 4)));
foreach ($items as $i) echo $i->input->divisibleby(2);
--EXPECT--
1