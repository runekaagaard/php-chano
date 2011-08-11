--TEST--
A generated testfile for the "truncatewordshtml" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(array('input' => '<p>one <a href="#">two - three <br>four</a> five</p>')));
foreach ($items as $i) echo $i->input->truncatewordshtml(4);
--EXPECT--
<p>one <a href="#">two - three <br>four ...</a></p>