--TEST--
A generated testfile for the "getdigit" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 'xyz')));
foreach ($items as $i) echo $i->input->getdigit(0);
--EXPECT--
xyz
