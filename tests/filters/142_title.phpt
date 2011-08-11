--TEST--
A generated testfile for the "title" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(
    array('input' => 'a nice title, isn\'t it?'),
    array('input' => "'SUP'ax' '' "),
));
foreach ($items as $i) echo $i->input->title() . "\n";
--EXPECT--
A Nice Title, Isn't It?
'sup'ax' ''