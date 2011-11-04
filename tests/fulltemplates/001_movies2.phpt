--TEST--
Testing rendering of a simple template.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$movies = new Chano(array(
    array(
        'title' => 'I am legend',
        'ratings' => array(45,56,23,89,12),
        'links' => array(
            'http://www.imdb.com/title/tt0480249/',
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
--EXPECT--
<div class="movies">
	<p>
		Showing 2 movies	</p>
    
			<div class="movie odd"/>
			<h1>
				1) 
				I AM LEGEND			</h1>

			<h2>Ratings</h2>
			<p>
				45, 56, 23, 89, 12			</p>
			
			<h2>Link</h2>
			<ul>
									<li><a href="http://www.imdb.com/title/tt0480249/" rel="nofollow">www.imdb.com/title/tt0480249/</a></li>							</ul>
		</div>
			<div class="movie even"/>
			<h1>
				2) 
				ZORRO			</h1>

			<h2>Rating</h2>
			<p>
				80			</p>
			
			<h2>Links</h2>
			<ul>
									<li><a href="http://www.imdb.com/title/tt0120746/" rel="nofollow">www.imdb....</a></li>
	<li><a href="mailto:zorro@example.com">zorro@exa...</a></li>
	<li><a href="ftp://trailerdownload.org" rel="nofollow">ftp://tra...</a></li>							</ul>
		</div>
	    
</div>
