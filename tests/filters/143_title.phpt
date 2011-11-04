--TEST--
A generated testfile for the "title" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 'discothèque')));
foreach ($items as $i) echo $i->input->title();
--EXPECT--
Discothèque