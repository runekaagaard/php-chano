--TEST--
A generated testfile for the "phone2numeric" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => '0800 flowers')));
foreach ($items as $i) echo $i->input->phone2numeric();
--EXPECT--
0800 3569377