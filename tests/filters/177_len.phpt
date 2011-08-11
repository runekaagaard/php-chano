--TEST--
A generated testfile for the "len" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => 'http://31characteruri.com/test/')));
foreach ($items as $i) echo $i->input->len();
--EXPECT--
31