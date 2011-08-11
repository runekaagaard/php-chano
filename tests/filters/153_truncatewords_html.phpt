--TEST--
A generated testfile for the "truncatewordshtml" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => 'Ångström was here')));
foreach ($items as $i) echo $i->input->truncatewordshtml(1);
--EXPECT--
Ångström ...
