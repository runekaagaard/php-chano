--TEST--
A generated testfile for the "filesizeformat" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => 1023)));
foreach ($items as $i) echo $i->input->filesizeformat();
--EXPECT--
1023 bytes