--TEST--
A generated testfile for the "escapejs" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => '\\ : backslashes, too')));
foreach ($items as $i) echo $i->input->escapejs();
--EXPECT--
\u005C : backslashes, too