--TEST--
Testing rendering of a simple template.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = new Chano(array(
    array(
        'title' => 'I am legend',
        'ratings' => array(45,56,23,89,12),
        'links' => array(
            'http://www.imdb.com/title/tt0480249/',
            'imdb.com/name/nm1349376/',
        ),
    ),
    array(
        'title' => 'zorro',
        'ratings' => array(80),
        'links' => array(
            'http://www.imdb.com/title/tt0120746/',
            'zorro@example.com',
            'ftp://trailerdownload.org',
        ),
    ),
));
?><?foreach ($items as $i):?>
    <div class="movies <?=$i->cycle('odd', 'even')?>">
        <h1><?=$i->title->capfirst()->rjust(20)?></h1>
        <p>
            <strong>Rating<?=$i->ratings->pluralize()?>:</strong><?=$i->ratings->join()->_?>    
        </p>
        <p>
            <ul>
                <?if($i->links->length() < 3):?>
                    <?=$i->links->unorderedlist()->urlize()?>
                <?else:?>
                    <?=$i->links->unorderedlist()->urlizetrunc(12)?>
                <?endif?>
            </ul>
        </p>
    </div>
<?endforeach?>
--EXPECT--
    <div class="movies odd">
        <h1>         I am legend</h1>
        <p>
            <strong>Ratings:</strong>45, 56, 23, 89, 12    
        </p>
        <p>
            <ul>
                                    <li><a href="http://www.imdb.com/title/tt0480249/" rel="nofollow">www.imdb.com/title/tt0480249/</a></li>
	<li>imdb.com/name/nm1349376/</li>                            </ul>
        </p>
    </div>
    <div class="movies even">
        <h1>               Zorro</h1>
        <p>
            <strong>Rating:</strong>80    
        </p>
        <p>
            <ul>
                                    <li><a href="http://www.imdb.com/title/tt0120746/" rel="nofollow">www.imdb....</a></li>
	<li><a href="mailto:zorro@example.com">zorro@exa...</a></li>
	<li><a href="ftp://trailerdownload.org" rel="nofollow">ftp://tra...</a></li>                            </ul>
        </p>
    </div>
