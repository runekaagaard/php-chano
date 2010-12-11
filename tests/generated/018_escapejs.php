<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => '<script>and this</script>')));
foreach ($items as $i) echo $i->input->escapejs();
