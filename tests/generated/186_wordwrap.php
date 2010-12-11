<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'this is a short paragraph of text.
  But this line should be indented')));
foreach ($items as $i) echo $i->input->wordwrap(15);
