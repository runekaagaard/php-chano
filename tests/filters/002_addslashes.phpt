--TEST--
A generated testfile for the "addslashes" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => '\\ : backslashes, too')));
foreach ($items as $i) echo $i->input->addslashes();
--EXPECT--
\\ : backslashes, too