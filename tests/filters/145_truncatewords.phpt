--TEST--
A generated testfile for the "truncatewords" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => 'A sentence with a few words in it')));
foreach ($items as $i) echo $i->input->truncatewords(5);
--EXPECT--
A sentence with a few ...