--TEST--
A generated testfile for the "filesizeformat" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => 52428800)));
foreach ($items as $i) echo $i->input->filesizeformat();
--EXPECT--
50.0 MB