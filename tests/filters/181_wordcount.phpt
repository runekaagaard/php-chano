--TEST--
A generated testfile for the "wordcount" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => '')));
foreach ($items as $i) echo $i->input->wordcount();
--EXPECT--
0