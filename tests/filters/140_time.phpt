--TEST--
A generated testfile for the "time" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => 13)));
foreach ($items as $i) echo $i->input->time('h');
--EXPECT--
01