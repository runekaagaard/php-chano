--TEST--
A generated testfile for the "getdigit" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => 123)));
foreach ($items as $i) echo $i->input->getdigit(3);
--EXPECT--
3
