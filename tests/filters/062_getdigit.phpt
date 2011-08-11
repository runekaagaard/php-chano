--TEST--
A generated testfile for the "getdigit" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => 123)));
foreach ($items as $i) echo $i->input->getdigit(2);
--EXPECT--
2
