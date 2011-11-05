.. highlight:: php

Chano template inheritance
==========================

Extending templates
-------------------

Template inheritance allows you to build a base "skeleton" template that 
contains all the common elements of your site and defines **blocks** that child 
templates can override.

It's easiest to understand template inheritance by starting with an example::

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <link rel="stylesheet" href="style.css" />
        <title><?Chano::block('title')?>My amazing site<?Chano::endblock()?></title>
    </head>

    <body>
        <div id="sidebar">
            <?Chano::block('sidebar')?>
            <ul>
                <li><a href="/">Home</a></li>
                <li><a href="/blog/">Blog</a></li>
            </ul>
            <?Chano::endblock()?>
        </div>

        <div id="content">
            <?Chano::block('content')?><?Chano::endblock()?>
        </div>
    </body>
    </html>

This template, which we'll call ``base.php``, defines a simple HTML skeleton
document that you might use for a simple two-column page. It's the job of
"child" templates to fill the empty blocks with content.

In this example, the ``Chano::block()`` method defines three blocks that child
templates can opt to fill in. A child template might look like this::

    <?Chano::extend()?>

        <?Chano::block('title')?>My amazing blog<?Chano::endblock()?>

        <?Chano::block('content')?>
            <?foreach(new Chano($blog_entries) as $entry)?>
                <h2><?=$entry->title?></h2>
                <p><?=$entry->body?></p>
            <?endforeach?>
        <?Chano::endblock()?>
        
    <?Chano::endextend()?><?require 'templates/base.php'?>

The ``Chano::extend()`` method is the key here. It tells the template engine 
that the blocks inside the ``<?Chano::extend()?><?Chano::endextend()?>``
construct extends another template. Deciding which template a child template
should extend is as simple as including that file right after the 
``endextend()`` method.

Depending on the value of ``$blog_entries``, the output might look like::

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <link rel="stylesheet" href="style.css" />
        <title>My amazing blog</title>
    </head>

    <body>
        <div id="sidebar">
            <ul>
                <li><a href="/">Home</a></li>
                <li><a href="/blog/">Blog</a></li>
            </ul>
        </div>

        <div id="content">
            <h2>Entry one</h2>
            <p>This is my first entry.</p>

            <h2>Entry two</h2>
            <p>This is my second entry.</p>
        </div>
    </body>
    </html>

Note that since the child template didn't define the ``sidebar`` block, the
value from the parent template is used instead. Content within a 
``<?Chano::block()`` tag in a parent template is always used as a fallback.
Blocks with no content or only whitespace are considered as being noexisting.
You can use as many levels of inheritance as needed. 

Super
-----

If you need to get the content of the block from the parent template, the 
``<?=Chano::super?>`` constant will do the trick. This is useful if you want to 
add to the contents of a parent block instead of completely overriding it. 

For example, if the child template looks like::

    <?Chano::extend()?>
        <?Chano::block('title')?><?=Chano::super?> blog<?Chano::endblock()?>
    <?Chano::endextend()?><?require dirname(__FILE__) . '/base.php'?>

And the base template like::

    <?Chano::block('title')?>This is my<?Chano::endblock()?>

The output would be ``This is my blog``.

Data inserted using ``<?Chano::super?>`` will not be automatically escaped, 
since it was already escaped, if necessary, in the parent template.
  
Usage recommendations
---------------------

One common way of using inheritance is the following three-level approach:

* Create a ``base.php`` template that holds the main look-and-feel of your
  site.
* Create a ``base_SECTIONNAME.php`` template for each "section" of your
  site. For example, ``base_news.php``, ``base_sports.php``. These
  templates all extend ``base.php`` and include section-specific
  styles/design.
* Create individual templates for each type of page, such as a news
  article or blog entry. These templates extend the appropriate section
  template.

This approach maximizes code reuse and makes it easy to add items to shared
content areas, such as section-wide navigation.

Here are some tips for working with inheritance:

* More ``<?Chano::block()`` tags in your base templates are better. 
  Remember, child templates don't have to define all parent blocks, so you can 
  fill in reasonable defaults in a number of blocks, then only define the ones
  you need later. It's better to have more hooks than fewer hooks.

* If you find yourself duplicating content in a number of templates, it
  probably means you should move that content to a ``<?Chano::block()`` in a
  parent template.

* For extra readability, you can optionally give a *name* to your
  ``<?Chano::endblock()?>`` method. For example::

      <?Chano::block('content')?>
      ...
      <?Chano::endblock('content')?>

  In larger templates, this technique helps you see which ``<?Chano::block()``
  tags are being closed.
