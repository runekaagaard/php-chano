--TEST--
A generated testfile for the "cut" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'a string to be mangled')));
foreach ($items as $i) echo $i->input->cut('ng');
--EXPECT--
a stri to be maled