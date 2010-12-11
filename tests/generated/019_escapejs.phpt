--TEST--
A generated testfile for the "escapejs" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'paragraph separator: and line separator: ')));
foreach ($items as $i) echo $i->input->escapejs();
--EXPECT--
paragraph separator:\u2029and line separator:\u2028