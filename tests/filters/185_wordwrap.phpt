--TEST--
A generated testfile for the "wordwrap" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 'this is a short paragraph of text.
  But this line should be indented')));
foreach ($items as $i) echo $i->input->wordwrap(14);
--EXPECT--
this is a
short
paragraph of
text.
  But this
line should be
indented