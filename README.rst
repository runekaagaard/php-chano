PHP Template Iterator
=====================

An irator class that takes an array of arrays as an input and supplies
capabilities resembling the Django Template Language.

Still extreme alpha.

Example
-------
The following is a example of usage::

{{{
#
    <?php
    require realpath(dirname(__FILE__)) . '/TIter.php';

    $items = array(
            array('id' => 1, 'titles' => array('Main' =>'<a', 'Sub'=> '2'), 'body' => 'x'),
            array('id' => 2, 'titles' => array('Main' =>'a', 'Sub'=> '2'), 'body' => 'xx'),
            array('id' => 3, 'titles' => array('Main' =>'b', 'Sub'=> '3'), 'body' => '<br />xxxx'),
            array('id' => 4, 'titles' => array('Main' =>'c', 'Sub'=> '3'), 'body' => ''),
    );
    ?>
    <?foreach(new TIter($items) as $_):?>
     <?=$_->body?>
     <?=$_['titles']['Main']?><?if($_['titles']['Main']->has_changed()):?>changed<?endif?>
     <?=$_->body?>
     <?=$_->titles->Sub?><?=$_->titles->Sub->same() ? ' same' : ' notsame'?>
     <?=$_->cycle('das', 'ist', 'gut')?>
     <?=$_->cycle('is', 'good')?>
     <?if($_->is_first()):?>isfirst!<?endif?>
     <?if($_->is_last()):?>islast!<?endif?>
     <?=$_->body->emptyor('Empty body')?>
     <?=$_->body->length()?>
     <?=$_->body->striptags()?>
     <?=$_->titles->Main->safe()?>

    <?endforeach?>
}}}