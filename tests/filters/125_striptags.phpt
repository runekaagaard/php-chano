--TEST--
A generated testfile for the "striptags" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 'some <b>html</b> with <script>alert("You smell")</script> disallowed <img /> tags')));
foreach ($items as $i) echo $i->input->striptags();
--EXPECT--
some html with alert("You smell") disallowed  tags