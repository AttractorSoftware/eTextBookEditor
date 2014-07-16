<?php
    require_once "lib/eTextBook.class.php";

    $bookContent = "";
    $bookTitle = "";
    if(isset($_GET['book'])) {
        $viewBook = new eTextBook($_GET['book']);
        if(isset($_GET['module'])) {
            $currentModule = $_GET['module'];
            $viewModuleContent = $viewBook->getModuleContent($currentModule);
        } else {
            $viewModuleContent = $viewBook->getFirstModuleContent();
        }
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

        <ol id="moduleList" class="breadcrumb">
            <?php
                $modules = $viewBook->getModules();
                foreach($modules as $module):
                    $currentModule = isset($currentModule) ? $currentModule : $modules[0];
            ?>
                <?php if($module != $currentModule): ?>
                    <li>
                        <a href="/editor.php?book=<?php echo $viewBook->getSlug()?>.etb&module=<?php echo $module; ?>">
                            <?php echo $module; ?>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="active">
                        <?php echo $module; ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
            <li><a href="#" id="addModuleBtn" data-toggle="modal" data-target="#moduleFormModal">Добавить модуль</a></li>
        </ol>

        <div class="modal fade" id="moduleFormModal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <h4 class="modal-title">Новый модуль</h4>
                    </div>
                    <form ng-controller="App.moduleForm.controller" name="moduleForm" book-slug="<?php echo $viewBook->getSlug(); ?>">
                        <div class="modal-body">
                            <div id="alertBox" style="display: none" class="alert alert-success" role="alert">QQQ</div>
                            <div class="form-group">
                                <label for="moduleTitle">
                                    Название модуля:
                                        <span class="label label-danger" ng-show="moduleForm.title.$error.required">
                                            обязательно для заполнения
                                        </span>
                                </label>
                                <input
                                    id="moduleTitle"
                                    class="form-control"
                                    type="text"
                                    name="title"
                                    ng-model="module.title"
                                    required />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button id="moduleFormClose" type="button" class="btn btn-default" data-dismiss="modal">
                                <span class="glyphicon glyphicon-remove"></span>
                                Закрыть
                            </button>
                            <button
                                id="moduleFormSubmit"
                                type="button"
                                class="btn btn-primary"
                                ng-click="submit(module)"
                                ng-disabled="moduleForm.$invalid || isUnchanged(book)"
                            >
                                <span class="glyphicon glyphicon-ok"></span>
                                Добавить модуль
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="e-text-book-editor" book="<?php echo $viewBook->getSlug(); ?>" module="<?php echo $currentModule; ?>">
            <div class="desktop">
                <?php echo isset($viewBook) ? $viewModuleContent : ''; ?>
            </div>
            <div class="display e-text-book-viewer"></div>
        </div>

    </div>

</div>

<?php require_once 'fileManager.php'; ?>
<?php require_once 'jsTemplates.php'; ?>

<link rel="stylesheet" type="text/css" href="css/main-style.min.css" />
<script src="js/script.min.js"></script>
<script src="js/lib/angular.min.js"></script>
<script src="js/eTextBook/moduleForm.js"></script>
</body>
</html>