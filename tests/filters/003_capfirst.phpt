--TEST--
A generated testfile for the "capfirst" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => 'hello world')));
foreach ($items as $i) echo $i->input->capfirst();
--EXPECT--
Hello world