--TEST--
A generated testfile for the "yesno" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => NULL)));
foreach ($items as $i) echo $i->input->yesno();
--EXPECT--
maybe