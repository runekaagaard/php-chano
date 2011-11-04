--TEST--
A generated testfile for the "truncatewordshtml" filter.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = new Chano(array(array('input' => '<p>one <a href="#">two - three <br>four</a> five</p>')));
foreach ($items as $i) echo $i->input->truncatewordshtml(100);
--EXPECT--
<p>one <a href="#">two - three <br>four</a> five</p>