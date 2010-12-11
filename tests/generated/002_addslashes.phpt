--TEST--
A generated testfile for the "addslashes" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => '\\ : backslashes, too')));
foreach ($items as $i) echo $i->input->addslashes();
--EXPECT--
\\ : backslashes, too