<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => ' Jack & Jill like numbers 1,2,3 and 4 and silly characters ?%.$!/')));
foreach ($items as $i) echo $i->input->slugify();
