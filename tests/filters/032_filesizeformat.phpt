--TEST--
A generated testfile for the "filesizeformat" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => 'α')));
foreach ($items as $i) echo $i->input->filesizeformat();
--EXPECT--
0 bytes