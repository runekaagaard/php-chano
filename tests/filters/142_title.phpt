--TEST--
A generated testfile for the "title" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(
    array('input' => 'ø nice title, Æsn\'t it?'),
    array('input' => "'SUP'ax' '' "),
));
foreach ($items as $i) echo $i->input->title() . "\n";
--EXPECT--
Ø Nice Title, Æsn't It?
'sup'ax' ''