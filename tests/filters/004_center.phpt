--TEST--
A generated testfile for the "center" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => 'test')));
foreach ($items as $i) echo $i->input->center(6);
--EXPECT--
 test 