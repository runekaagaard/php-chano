--TEST--
A generated testfile for the "ljust" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => 'tøst')));
foreach ($items as $i) echo ":" . $i->input->ljust(10) . ":";
--EXPECT--
:tøst      :