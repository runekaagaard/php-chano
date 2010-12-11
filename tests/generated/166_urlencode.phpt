--TEST--
A generated testfile for the "urlencode" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'françois & jill')));
foreach ($items as $i) echo $i->input->urlencode();
--EXPECT--
fran%C3%A7ois%20%26%20jill