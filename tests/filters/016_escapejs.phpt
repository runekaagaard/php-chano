--TEST--
A generated testfile for the "escapejs" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => '\\ : backslashes, too')));
foreach ($items as $i) echo $i->input->escapejs();
--EXPECT--
\u005C : backslashes, too