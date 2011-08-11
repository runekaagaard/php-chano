--TEST--
A generated testfile for the "wordwrap" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => 'this is a short paragraph of text.
  But this line should be indented')));
foreach ($items as $i) echo $i->input->wordwrap(16);
--EXPECT--
this is a short
paragraph of
text.
  But this line
should be
indented