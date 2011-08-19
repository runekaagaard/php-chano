--TEST--
Testing date locale
--FILE--
<?php
// Needs on ubuntu to pass: sudo locale-gen da_DK.utf8.
date_default_timezone_set('UTC');
include dirname(__FILE__) . '/../../chano/Chano.php';
define ('CHANO_TESTS_NOWTIME', 946684800);
$items = array(
    array('d' => ""),
    array('d' => "Joel is a slug"),
    array('d' => "øæåØÆøæåØÆÅØÆÅøæå"),
);
foreach (new Chano($items) as $i) echo ":" . $i->d->truncatechars(8) . ":\n";
--EXPECT--
::
:Joel ...:
:øæåØÆ...:
