<?php

error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 1);

define('CHANO_GENDOC_BASEPATH', realpath(dirname(__FILE__)));

require CHANO_GENDOC_BASEPATH . '/../chano/Chano.php';
require CHANO_GENDOC_BASEPATH . '/bootstrap_docblox.php';

$flag =
"
Flags
_____

Sets one or more boolean values on the Chano class. Chainable.

";

$filter =
"
Filters
_____

Modifies the value of the current item. Chainable.

";

$methods = get_class_methods('Chano');

$rst =
".. highlight:: php

Chano functions
===============

Stub. Describe the different types here.
";
$prev_chanotype = false;
foreach ($methods as $method) {
    $rf_method = new ReflectionMethod('Chano', $method);
    $docblox = new DocBlox_Reflection_DocBlock($rf_method->getDocComment());
    $chanotype_tag = $docblox->getTagsByName('chanotype');
    if (empty($chanotype_tag)) continue;
    $chanotype = $chanotype_tag[0]->getContent();
    if (empty($prev_chanotype) || $prev_chanotype != $chanotype) {
        $rst .= $$chanotype;
    }


    ob_start();

?>.. _<?=$method?>:

<?=$method?>

<?=str_repeat('+', strlen($method))?>


<?=$docblox->getShortDescription()?>


<?=$docblox->getLongDescription()->getContents()?>

<? $rst .= ob_get_clean();

    $tags = $docblox->getTagsByName('param');
    if (!empty($tags)) {
        $rst .=
"
Arguments
~~~~~~~~~

";
    
        foreach ($tags as $tag) {
            list($name, $content) = array($tag->getName(), $tag->getContent());
            $rst .= "- ``$name`` - $content\n";
        }
    }

    $tags = $docblox->getTagsByName('return');
    if (!empty($tags)) {
        foreach ($tags as $tag) {
            list($name, $content) = array($tag->getName(), $tag->getContent());
            $rst .= "*Returns*\n  ``$content``\n\n";
        }
    }


    $prev_chanotype = $chanotype;
}

echo $rst . "\n";