--TEST--
A generated testfile for the "urlencode" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 'françois & jill')));
foreach ($items as $i) echo $i->input->urlencode();
--EXPECT--
fran%C3%A7ois+%26+jill
