--TEST--
A generated testfile for the "truncatewords" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => 123)));
foreach ($items as $i) echo $i->input->truncatewords(2);
--EXPECT--
123