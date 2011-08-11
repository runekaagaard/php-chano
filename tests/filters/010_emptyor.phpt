--TEST--
A generated testfile for the "default" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => 'val')));
foreach ($items as $i) echo $i->input->emptyor('default');
--EXPECT--
val
