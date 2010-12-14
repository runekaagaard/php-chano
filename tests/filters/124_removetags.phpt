--TEST--
A generated testfile for the "removetags" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'some <b>html</b> with <script>alert("You smell")</script> disallowed <img /> tags')));
foreach ($items as $i) echo $i->input->removetags('script', 'img');
--EXPECT--
some <b>html</b> with alert("You smell") disallowed  tags
