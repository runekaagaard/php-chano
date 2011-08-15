<?php

error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 1);

date_default_timezone_set('UTC');
$dt = new DateTime("2000-01-01");

define('CHANO_GENDOC_BASEPATH', realpath(dirname(__FILE__)));

require CHANO_GENDOC_BASEPATH . '/../chano/Chano.php';
require CHANO_GENDOC_BASEPATH . '/bootstrap_docblox.php';

$chano_src = file_get_contents(CHANO_GENDOC_BASEPATH . '/../chano/Chano.php');
$methods = get_class_methods('Chano');

$rst =
".. highlight:: php

Chano functions
===============

This document describes all the functions that Chano supports. Most of this 
documentation is adapated from
https://docs.djangoproject.com/en/dev/ref/templates/builtins/.

Should you wish to use a Chano function on a single value (string, int, array,
 etc.) this is possible by using the ``Chano::set()`` shortcut.
 
For example::

   <?=\$item->value->center(14)?>
        
Used inside a ``foreach`` loop is identical to::
        
   <?=Chano::set(\$value)->center(14))?>

This works for functions that works on the base Chano instance too. Simply
don't pass any arguments to the ``set()`` function.

For example::

   <?=\$item->now()?>
        
Used inside a ``foreach`` loop is identical to::
        
   <?=Chano::set()->now())?>

";
$prev_chanotype = false;
foreach ($methods as $method) {
    $rf_method = new ReflectionMethod('Chano', $method);
    $docblox = new DocBlox_Reflection_DocBlock($rf_method->getDocComment());
    $short = $docblox->getShortDescription();
    if (empty($short)) continue;
    $chanotype_tag = $docblox->getTagsByName('chanotype');
    if (empty($chanotype_tag)) continue;
    preg_match("#function ($method\(.*\))#Uis", $chano_src, $ms);
    $method_sig = $ms[1];
    $chanotype = $chanotype_tag[0]->getContent();
    if (empty($prev_chanotype) || $prev_chanotype != $chanotype) {
        preg_match("#/\*\*\n     \* @section $chanotype.*\*/#Uis", $chano_src, $ms);
        if (!empty($ms)) {
            $db = new DocBlox_Reflection_DocBlock($ms[0]);
            $_tags = $db->getTagsByName('section');
            $lines = explode("\n", $_tags[0]->getDescription());
            array_shift($lines);
            $headline = array_shift($lines);
            array_unshift($lines, "");
            array_unshift($lines, str_repeat('_', strlen($headline)));
            array_unshift($lines, $headline);
            $rst .= "\n" . implode("\n", $lines) . "\n\n";
        }
        
    }


    ob_start();

?>.. _<?=$method?>:

<?=$method_sig?>

<?=str_repeat('+', strlen($method_sig))?>


<?=$docblox->getShortDescription()?>


<?=$docblox->getLongDescription()->getContents()?>

<? $rst .= ob_get_clean();

    $tags = $docblox->getTagsByName('param');
    if (!empty($tags)) {
        $rst .=
"
Arguments

";
    
        foreach ($tags as $tag) {
            list($name, $content) = array($tag->getName(), $tag->getContent());
            $content = str_replace("\n", ' - ', $content);
            $rst .= "- ``$content``\n";
        }
    }

    $tags = $docblox->getTagsByName('return');
    if (!empty($tags)) {
        foreach ($tags as $tag) {
            list($name, $content) = array($tag->getName(), $tag->getContent());
            $rst .= "\n*Returns*\n  ``$content``\n\n";
        }
    }


    $prev_chanotype = $chanotype;
}

echo $rst . "\n";