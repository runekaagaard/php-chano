--TEST--
A generated testfile for the "fixampersands" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'Jack & Jill & Jeroboam')));
foreach ($items as $i) echo $i->input->fixampersands();
--EXPECT--
Jack &amp; Jill &amp; Jeroboam