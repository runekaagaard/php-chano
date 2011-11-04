--TEST--
A generated testfile for the "cut" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 'a string to be mangled')));
foreach ($items as $i) echo $i->input->cut('strings');
--EXPECT--
a string to be mangled