--TEST--
A generated testfile for the "linenumbers" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 123)));
foreach ($items as $i) echo $i->input->linenumbers();
--EXPECT--
1. 123