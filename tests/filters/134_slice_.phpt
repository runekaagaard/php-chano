--TEST--
A generated testfile for the "slice_" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 'øæådefg')));
foreach ($items as $i) echo $i->input->slice('1:3');
--EXPECT--
æå
