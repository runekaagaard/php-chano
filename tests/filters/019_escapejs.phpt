--TEST--
A generated testfile for the "escapejs" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => "paragraph separator:\xe2\x80\xa9and line separator:\xe2\x80\xa8")));
foreach ($items as $i) echo $i->input->escapejs();
--EXPECT--
paragraph separator:\u2029and line separator:\u2028
