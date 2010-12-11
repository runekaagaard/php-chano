<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'this is a long paragraph of text that really needs to be wrapped I\'m afraid')));
foreach ($items as $i) echo $i->input->wordwrap(14);
