--TEST--
A generated testfile for the "first" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'test')));
try {
    foreach ($items as $i) echo $i->input->first();
} catch (TypeNotArrayError $e) {
    die('caught!');
}
--EXPECT--
caught!