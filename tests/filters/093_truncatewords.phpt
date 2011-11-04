--TEST--
A generated testfile for the "truncatewords" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 123)));
foreach ($items as $i) echo $i->input->truncatewords(2);
--EXPECT--
123