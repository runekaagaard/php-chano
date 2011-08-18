.. highlight:: php

Welcome to the documentation for Chano.
=======================================

Chano is an almost direct port of Djangos Template Language to PHP. But where
Django uses its own compiled template language, Chano is implemented in pure 
PHP, which lets you easily enhance your existing PHP templates with Chano.

Chano consists of a single class ``Chano()`` -- a PHP iterator that lets you 
loop over an array, stdClass or Iterator and output its content in a variety of 
ways. Chano can of course be applied to a single value, e.g. a string, as well.

Chano is - thanks to the great Django documentation - pretty well documented.
Learn all about Chano by following the links below or get the code from the
github project at https://github.com/runekaagaard/php-chano and start hacking.

Table of contents
=================

.. toctree::
   :maxdepth: 1

   usage
   functions

Example
=======

Below follows an example of what a template using Chano could look like::

    <?php
    // This would probably be done in the controller.
    $movies = new Chano(get_movies());
    ?>

    <!-- Template -->
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