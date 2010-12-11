--TEST--
A generated testfile for the "urlize" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'info@djangoproject.org')));
foreach ($items as $i) echo $i->input->urlize();
--EXPECT--
<a href="mailto:info@djangoproject.org">info@djangoproject.org</a>