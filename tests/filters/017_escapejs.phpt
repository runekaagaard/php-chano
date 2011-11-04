--TEST--
A generated testfile for the "escapejs" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => "and lots of whitespace: \r\n\t\v\f")));
foreach ($items as $i) echo $i->input->escapejs();
--EXPECT--
and lots of whitespace: \u000D\u000A\u0009\u000B\u000C