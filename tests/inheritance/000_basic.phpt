--TEST--
Basic inheritance functionality.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
require dirname(__FILE__) . '/blog.php';
--EXPECT--
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