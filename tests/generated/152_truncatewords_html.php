<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => '<p>one <a href="#">two - three <br>four</a> five</p>')));
foreach ($items as $i) echo $i->input->truncatewords_html(100);
