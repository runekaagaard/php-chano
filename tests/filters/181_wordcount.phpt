--TEST--
A generated testfile for the "wordcount" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => '')));
foreach ($items as $i) echo $i->input->wordcount();
--EXPECT--
0