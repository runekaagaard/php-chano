<?php
require realpath(dirname(__FILE__)) . '/DtlIter.php';

$items = array(
        array('id' => 1, 'titles' => array('Main' =>"<a'", 'Sub'=> '2'),
            'body' => 'x', 'a'=> '', 'b' => 0, 'c' => 'Show me', 'x' => 92),
        array('id' => 2, 'titles' => array('Main' =>'a', 'Sub'=> '2'),
            'body' => 'xx', 'a'=> 'Show me', 'b' => '', 'c' => 'Dont show', 'x' => 63),
        array('id' => 3, 'titles' => array('Main' =>'b', 'Sub'=> '3', 'x' => 21),
            'body' => '<br />xxxx', 'a'=> '', 'b' => 'Show me', 'c' => 'Blah', 'x' => 65),
        array('id' => 4, 'titles' => array('Main' =>'c', 'Sub'=> '3'),
            'body' => '', 'a'=> 0, 'b' => null, 'c' => false, 'x' => 120),
);
?>
<?foreach(new DtlIter($items) as $i):?>
 <?=$i->body?>
 <?=$i['titles']['Main']?><?if($i['titles']['Main']->haschanged()):?>changed<?endif?>
 <?=$i->body?>
 <?=$i->titles->Sub?><?=$i->titles->Sub->same() ? ' same' : ' notsame'?>
 <?=$i->cycle('das', 'ist', 'gut')?>
 <?=$i->cycle('is', 'good')?>
 <?if($i->isfirst()):?>isfirst!<?endif?>
 <?if($i->islast()):?>islast!<?endif?>
 <?=$i->body->emptyor('Empty body')?>
 <?=$i->body->length()?>
 <?=$i->body->striptags()?>
 <?=$i->titles->Main->safe()?>
 <?=$i->autoescape_off()?><?=$i->titles->Main?>
 <?=$i->autoescape_on()?><?=$i->titles->Main?>
 <?=$i->firstof($i->a->_, $i->b->_, $i->c->_, 'Default')?>
 <?=$i->now('jS F Y H:i')?>
 <?=$i->x->widthratio(120, 100)?>
 <?=$i->x->add(2)?>
 <?=$i->titles->Main->addslashes()?>
 <?=$i->titles->Main->capfirst()?>
 <?=$i->titles->Main->center(4)?>

<?endforeach?>
<?
$items = array(
    array('a' => '<a>æ', 'd' => '9983439843', 'n' => 7,),
    array('a' => 'b', 'd' => '998343984', 'n' => 8,),
    array('a' => '>c>', 'd' => '9983430000', 'n' => 9,),
);
?>
 
<?foreach(new DtlIter($items) as $i):?>
<?=$i->a->cut('<')?>
 / <?=$i->d->date('D d M Y');?>
 / <?if($i->n->divisibleby(3)):?><?=$i->n?> is div. by 3<?endif?>
 / <?=$i->a->escape()?>
 
<?endforeach?>