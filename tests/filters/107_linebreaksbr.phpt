--TEST--
A generated testfile for the "linebreaksbr" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => 123)));
foreach ($items as $i) echo $i->input->linebreaksbr();
--EXPECT--
123