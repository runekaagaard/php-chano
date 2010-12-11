--TEST--
A generated testfile for the "truncatewords_html" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => '<p>one <a href="#">two - three <br>four</a> five</p>')));
foreach ($items as $i) echo $i->input->truncatewords_html(0);
--EXPECT--
