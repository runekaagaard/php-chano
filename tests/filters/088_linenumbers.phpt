--TEST--
A generated testfile for the "linenumbers" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => 123)));
foreach ($items as $i) echo $i->input->linenumbers();
--EXPECT--
1. 123