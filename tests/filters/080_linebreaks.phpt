--TEST--
A generated testfile for the "linebreaks" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => 'line 1
line 2')));
foreach ($items as $i) echo $i->input->linebreaks();
--EXPECT--
<p>line 1<br />line 2</p>