--TEST--
A generated testfile for the "slice_" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => 'øæådefg')));
foreach ($items as $i) echo $i->input->slice('1:3');
--EXPECT--
æå
