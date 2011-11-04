--TEST--
A generated testfile for the "escapejs" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => '"double quotes" and \'single quotes\'')));
foreach ($items as $i) echo $i->input->escapejs();
--EXPECT--
\u0022double quotes\u0022 and \u0027single quotes\u0027