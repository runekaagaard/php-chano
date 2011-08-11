--TEST--
A generated testfile for the "filesizeformat" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => 2.2517998136852E+18)));
foreach ($items as $i) echo $i->input->filesizeformat();
--EXPECT--
2000.0 PB