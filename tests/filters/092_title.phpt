--TEST--
A generated testfile for the "title" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => 123)));
foreach ($items as $i) echo $i->input->title();
--EXPECT--
123