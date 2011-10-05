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
        <?Chano::block('content')?>
            <?if(Chano::block_is_on()):?>
                My slow content. <?sleep(10)?>
            <?endif?>
        <?Chano::endblock()?>
    </div>
</body>
</html>