--TEST--
A generated testfile for the "center" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => 'tøæt')));
foreach ($items as $i) echo ":" . $i->input->center(6) . ":";
--EXPECT--
: tøæt :