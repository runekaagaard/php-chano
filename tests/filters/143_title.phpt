--TEST--
A generated testfile for the "title" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => 'discothèque')));
foreach ($items as $i) echo $i->input->title();
--EXPECT--
Discothèque