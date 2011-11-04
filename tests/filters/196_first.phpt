--TEST--
A generated testfile for the "first" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => array())));
try { foreach ($items as $i) echo $i->input->first();
} catch (Chano_ValueIsEmptyError $e) { echo 1; }

$items = new Chano(array(array('input' => new stdClass)));
try { foreach ($items as $i) echo $i->input->first(); }
catch (Chano_ValueIsEmptyError $e) { echo 2; }
--EXPECT--
12