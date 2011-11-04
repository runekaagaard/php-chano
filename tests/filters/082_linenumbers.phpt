--TEST--
A generated testfile for the "linenumbers" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 'x
x
x
x
x
x
x
x
x
x')));
foreach ($items as $i) echo $i->input->linenumbers();
--EXPECT--
01. x
02. x
03. x
04. x
05. x
06. x
07. x
08. x
09. x
10. x