--TEST--
A generated testfile for the "getdigit" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => 'xyz')));
foreach ($items as $i) echo $i->input->getdigit(0);
--EXPECT--
xyz
