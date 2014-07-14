<?php
    require_once "lib/eTextBook.class.php";

    $bookContent = "";
    $bookTitle = "";
    if(isset($_GET['book'])) {
        $viewBook = new eTextBook($_GET['book']);
    }
?>

<!Doctype html>
<html lang="ru" ng-app>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta charset="utf-8"/>
    <title>Учебники</title>
    <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/css/style.css"/>
</head>
<body class="without-padding">
<div class="page-headline">
    <nav class="navbar navbar-default main-menu" role="navigation">
        <div class="container">
            <ul class="nav navbar-nav">
                <li><a href="#">Мой профиль</a></li>
                <li class="active"><a href="#">Учебники</a></li>
            </ul>
        </div>
    </nav>
    <div class="container">

        <div class="page-header">
            <h1><?php echo $viewBook->getTitle(); ?></h1>
        </div>

        <ol class="breadcrumb">
            <li>
                <span class="glyphicon glyphicon-book"></span>
                <a href="/books.php">Учебники</a>
            </li>
            <li>
                <span class="glyphicon glyphicon-th-list"></span>
                <a href="/books.php">Cписок учебников</a>
            </li>
            <li>
                <span class="glyphicon glyphicon-book"></span>
                <a href="#"><?php echo $viewBook->getTitle(); ?></a>
            </li>
        </ol>

        <div class="e-text-book-editor">
            <div class="desktop"><?php echo isset($viewBook) ? $viewBook->getContent() : ''; ?></div>
            <div class="display e-text-book-viewer"></div>
        </div>

    </div>

</div>

<?php require_once 'fileManager.php'; ?>
<?php require_once 'jsTemplates.php'; ?>

<link rel="stylesheet" type="text/css" href="css/main-style.min.css" />
<script src="js/script.min.js"></script>
</body>
</html>