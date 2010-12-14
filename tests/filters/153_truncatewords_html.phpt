--TEST--
A generated testfile for the "truncatewordshtml" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'Ångström was here')));
foreach ($items as $i) echo $i->input->truncatewordshtml(1);
--EXPECT--
Ångström ...
