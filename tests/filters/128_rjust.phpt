--TEST--
A generated testfile for the "rjust" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => 'test')));
foreach ($items as $i) echo $i->input->rjust(10);
--EXPECT--
      test