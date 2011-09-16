--TEST--
A generated testfile for the "truncatewords" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => 'Ø øæp with a few words in it')));
foreach ($items as $i) echo $i->input->truncatewords(5);
--EXPECT--
Ø øæp with a few ...