--TEST--
A generated testfile for the "yesno" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => false)));
foreach ($items as $i) echo $i->input->yesno('certainly','get out of town','perhaps');
--EXPECT--
get out of town
