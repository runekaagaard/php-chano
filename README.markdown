## Chano ##

Chano is an almost direct port of Djangos Template Language to PHP. But where
Django uses its own template language, Chano is implemented as a PHP iterator
that lets you iterate over an array, stdClass or Iterator and manipulate its
content in a variety of ways, matching the capabilities of the Django Template
Language.

Besides from being able to iterate over data, all the Chano functions can also
be called on non iterable values.

Read the docs at http://chano.readthedocs.org/.

## Example ##

Below follows an example of what a template using Chano could look like:

```php
<?foreach(new Chano($items) as $i):?>
    <div class="movies <?=$i->cycle('odd', 'even')?>">
        <h1><?=$i->title->capfirst()->ljust(20)?></h1>
        <p>Number <?=$i->counter()?>.</p>
        <p>
            <strong>Rating<?=$i->ratings->pluralize()?>:</strong>
            <?=$i->ratings->join()?>
        </p>
        <ul>
            <?if($i->links->length() < 3):?>
                <?=$i->links->unorderedlist()->urlize()?>
            <?else:?>
                <?=$i->links->unorderedlist()->urlizetrunc(12)?>
            <?endif?>
        </ul>
    </div>
<?endforeach?>
```
