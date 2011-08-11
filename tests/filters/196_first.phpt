--TEST--
A generated testfile for the "first" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => array())));
try { foreach ($items as $i) echo $i->input->first();
} catch (Chano_ValueIsEmptyError $e) { echo 1; }

$items = new Chano(array(array('input' => new stdClass)));
try { foreach ($items as $i) echo $i->input->first(); }
catch (Chano_ValueIsEmptyError $e) { echo 2; }
--EXPECT--
12