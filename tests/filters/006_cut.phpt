--TEST--
A generated testfile for the "cut" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => 'a striøæng to be maøængled')));
foreach ($items as $i) echo $i->input->cut('øæng');
--EXPECT--
a stri to be maled