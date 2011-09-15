--TEST--
A generated testfile for the "length" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => 'abcdøæåØÆÅ')));
foreach ($items as $i) echo $i->input->length();
--EXPECT--
10