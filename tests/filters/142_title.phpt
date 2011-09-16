--TEST--
A generated testfile for the "title" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(
    array('input' => 'ø nice title, Æsn\'t it?'),
    array('input' => "'SUP'ax' '' "),
));
foreach ($items as $i) echo $i->input->title() . "\n";
--EXPECT--
Ø Nice Title, Æsn't It?
'sup'ax' ''