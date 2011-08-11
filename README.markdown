## Chano ##

An iterator class that takes an array of things as an input and supplies
capabilities resembling the Django Template Language.

Filters are chainable where it makes sense.

Still in beta.

## Example ##
Also check out the the tests. The following is a example of usage::

```php
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
?>
<?foreach ($items as $i):?>
    <div class="movies <?=$i->cycle('odd', 'even')?>">
        <h1><?=$i->title->capfirst()->ljust(20)?></h1>
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
```