--TEST--
A generated testfile for the "filesizeformat" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 'α')));
foreach ($items as $i) echo $i->input->filesizeformat();
--EXPECT--
0 bytes