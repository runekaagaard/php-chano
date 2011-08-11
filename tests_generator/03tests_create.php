<?php
define('CHANO_PATH_BASE', realpath(dirname(__FILE__)));
define('CHANO_PATH_OUTPUT', CHANO_PATH_BASE . '/../tests/generated');
$tests = unserialize(file_get_contents(CHANO_PATH_BASE . '/tests.serialized'));
$i = 0;
foreach ($tests as $t) {
    $args = $t['input'];
    $v = array_shift($args);
    foreach ($args as &$arg) $arg = var_export($arg, true);
    $args_as_string = implode(',', $args);
    ob_start();
?>--TEST--
A generated testfile for the "<?=$t['filter']?>" filter.
--FILE--
<?='<?php'?>

include dirname(__FILE__) . '/../../Chano.php';
$items = new Chano(array(array('input' => <?=var_export($v)?>)));
foreach ($items as $i) echo $i->input-><?=$t['filter']?>(<?=$args_as_string?>);
--EXPECT--
<?=$t['output']?>
<?
$n = str_pad($i, 3, "0", STR_PAD_LEFT);
file_put_contents(CHANO_PATH_OUTPUT . "/{$n}_{$t['filter']}.phpt", ob_get_clean());
++$i;
}
