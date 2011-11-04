--TEST--
A generated testfile for the "len" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 'http://31characteruri.com/øæåæ/')));
foreach ($items as $i) echo $i->input->length();
--EXPECT--
31
