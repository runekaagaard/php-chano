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
<?php
// This would probably be done in the controller.
$movies = new Chano(get_movies());
?>

<!-- Template -->

<h1>
    <?=Chano::with($title)->upper()?>
</h1>

<div class="movies">
	<p>
		Showing <?=$movies->length()?> movie<?=$movies->pluralize()?>
	</p>
    
	<?foreach($movies as $m):?>
		<div class="movie <?=$m->cycle('odd', 'even')?>"/>
			<h1>
				<?=$m->counter()?>) 
				<?=$m->title->upper()?>
			</h1>

			<h2>Rating<?=$m->ratings->pluralize()?></h2>
			<p>
				<?=$m->ratings->join()?>
			</p>
			
			<h2>Link<?=$m->links->pluralize()?></h2>
			<ul>
				<?if($m->links->length() < 3):?>
					<?=$m->links->unorderedlist()->urlize()?>
				<?else:?>
					<?=$m->links->unorderedlist()->urlizetrunc(12)?>
				<?endif?>
			</ul>
		</div>
	<?endforeach?>
    
</div>
```

## Tests ##
Chano is pretty well tested with PHPT. I was able to port most of the tests for
the functions directly from Django and then add integration tests to those.

See: https://github.com/runekaagaard/php-chano/tree/master/tests.

