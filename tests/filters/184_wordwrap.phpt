--TEST--
A generated testfile for the "wordwrap" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => 'this is a long paragraph of text that really needs to be wrapped I\'m afraid')));
foreach ($items as $i) echo $i->input->wordwrap(14);
--EXPECT--
this is a long
paragraph of
text that
really needs
to be wrapped
I'm afraid