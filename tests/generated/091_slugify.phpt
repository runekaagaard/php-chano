--TEST--
A generated testfile for the "slugify" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 123)));
foreach ($items as $i) echo $i->input->slugify();
--EXPECT--
123