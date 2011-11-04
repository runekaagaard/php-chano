--TEST--
A generated testfile for the "filesizeformat" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 1073741824)));
foreach ($items as $i) echo $i->input->filesizeformat();
--EXPECT--
1.0 GB