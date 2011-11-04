--TEST--
A generated testfile for the "truncatewords" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 'A sentence with a few words in it')));
foreach ($items as $i) echo $i->input->truncatewords('not a number');
--EXPECT--
A sentence with a few words in it