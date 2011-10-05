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