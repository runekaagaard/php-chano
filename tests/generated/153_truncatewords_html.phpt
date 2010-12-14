--TEST--
A generated testfile for the "truncatewordshtml" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
mb_internal_encoding('utf-8');
$items = new DtlIter(array(array('input' => 'Ångström was here')));
foreach ($items as $i) echo $i->input->truncatewordshtml(1);
--EXPECT--
Ångström ...
