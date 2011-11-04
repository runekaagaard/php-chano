--TEST--
A generated testfile for the "truncatewordshtml" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 'Ångström was here')));
foreach ($items as $i) echo $i->input->truncatewordshtml(1);
--EXPECT--
Ångström ...
