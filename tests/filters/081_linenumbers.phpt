--TEST--
A generated testfile for the "linenumbers" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 'line 1
line 2')));
foreach ($items as $i) echo $i->input->linenumbers();
--EXPECT--
1. line 1
2. line 2