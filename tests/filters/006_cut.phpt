--TEST--
A generated testfile for the "cut" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 'a striøæng to be maøængled')));
foreach ($items as $i) echo $i->input->cut('øæng');
--EXPECT--
a stri to be maled