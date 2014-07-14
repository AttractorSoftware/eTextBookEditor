<?php
    include_once('lib/eTextBook.class.php');
    $bookFiles = Util::fileList(Util::getRootDir() . 'books');
    $books = array();
    foreach($bookFiles as $file) {
        $books[] = new eTextBook($file);
    }
?>

<!Doctype html>
<html lang="ru" ng-app>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta charset="utf-8" />
        <title>Учебники</title>
        <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="/css/style.css" />
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
                    <h1>Список учебников</h1>
                </div>

                <ol class="breadcrumb">
                    <li>
                        <span class="glyphicon glyphicon-book"></span>
                        <a href="#">Учебники</a></li>
                    <li>
                        <span class="glyphicon glyphicon-th-list"></span>
                        <a href="#">Cписок учебников</a></li>
                </ol>

                <ul class="list-unstyled book-list">
                    <li>
                        <span class="glyphicon glyphicon-file"></span>
                        <a href="#" data-toggle="modal" data-target="#bookFormModal">Создать новый учебник</a>

                        <div class="modal fade" id="bookFormModal" role="dialog">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span aria-hidden="true">&times;</span>
                                            <span class="sr-only">Close</span>
                                        </button>
                                        <h4 class="modal-title">Новый учебник</h4>
                                    </div>
                                    <form ng-controller="App.bookForm.controller" name="bookForm">
                                        <div class="modal-body">
                                            <div id="alertBox" style="display: none" class="alert alert-success" role="alert">QQQ</div>
                                            <div class="form-group">
                                                <label for="newBookTitle">
                                                    Название учебника:
                                                    <span class="label label-danger" ng-show="bookForm.title.$error.required">
                                                        обязательно для заполнения
                                                    </span>
                                                </label>
                                                <input
                                                    id="newBookTitle"
                                                    class="form-control"
                                                    type="text"
                                                    name="title"
                                                    ng-model="book.title"
                                                    required />
                                            </div>

                                            <div class="form-group">
                                                <label for="newBookTitle">
                                                    Авторы:
                                                </label>
                                                <input
                                                    id="newBookTitle"
                                                    class="form-control"
                                                    type="text"
                                                    ng-model="book.authors"
                                                    name="authors" />
                                            </div>

                                            <div class="form-group">
                                                <label for="newBookTitle">Под редакцией:</label>
                                                <input
                                                    id="newBookTitle"
                                                    class="form-control"
                                                    type="text"
                                                    name="editor"
                                                    ng-model="book.editor" />
                                            </div>

                                            <div class="form-group">
                                                <label for="newBookTitle">ISBN:</label>
                                                <input
                                                    id="newBookTitle"
                                                    class="form-control"
                                                    type="text"
                                                    name="isbn"
                                                    ng-model="book.isbn" />
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">
                                                <span class="glyphicon glyphicon-remove"></span>
                                                Закрыть
                                            </button>
                                            <button type="button" class="btn btn-primary" ng-click="submit(book)" ng-disabled="bookForm.$invalid || isUnchanged(book)">
                                                <span class="glyphicon glyphicon-ok"></span>
                                                Создать учебник
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </li>
                    <li>
                        &nbsp;
                    </li>
                    <?php foreach($books as $book): ?>
                        <li>
                            <span class="glyphicon glyphicon-book"></span>
                            <a href="/editor.php?book=<?php echo $book->getSlug(); ?>.etb">
                                <?php echo $book->getTitle(); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

            </div>

        </div>
        <script src="/js/lib/jquery-2.1.1.min.js"></script>
        <script src="/js/lib/bootstrap.min.js"></script>
        <script src="/js/lib/angular.min.js"></script>
        <script src="/js/app.js"></script>
        <script src="/js/eTextBook/bookForm.js"></script>
    </body>
</html>