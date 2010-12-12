--TEST--
A generated testfile for the "escapejs" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => '<script>and this</script>')));
foreach ($items as $i) echo $i->input->escapejs();
--EXPECT--
\u003Cscript\u003Eand this\u003C\u002Fscript\u003E
