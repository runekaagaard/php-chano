--TEST--
A generated testfile for the "linebreaks" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => 'line 1')));
foreach ($items as $i) echo $i->input->linebreaks();
--EXPECT--
<p>line 1</p>