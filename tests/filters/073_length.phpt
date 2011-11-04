--TEST--
A generated testfile for the "length" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 'abcdøæåØÆÅ')));
foreach ($items as $i) echo $i->input->length();
--EXPECT--
10