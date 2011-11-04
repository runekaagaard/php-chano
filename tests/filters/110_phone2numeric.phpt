--TEST--
A generated testfile for the "phone2numeric" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => '0800 flowers')));
foreach ($items as $i) echo $i->input->phone2numeric();
--EXPECT--
0800 3569377