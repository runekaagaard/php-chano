--TEST--
A generated testfile for the "yesno" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => NULL)));
foreach ($items as $i) echo $i->input->yesno('certainly', 'get out of town');
--EXPECT--
get out of town
