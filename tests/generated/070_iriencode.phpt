--TEST--
A generated testfile for the "iriencode" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'Sør-Trøndelag')));
foreach ($items as $i) echo $i->input->iriencode();
--EXPECT--
S%C3%B8r-Tr%C3%B8ndelag