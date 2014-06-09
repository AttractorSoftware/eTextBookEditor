<!Doctype html>
<html lang="ru" ng-app>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta charset="utf-8" />
        <title>Учебник кыргызского языка - 7 класс</title>
    </head>
    <body>
        <div class="container">
            <div class="e-text-book-editor">
                <div class="properties">
                    <div class="container">
                        <div style="float: left; margin: 10px 0 0 20px">
                            <label for="book-title">Название учебника:</label>
                            <input
                                type="text"
                                id="book-title"
                                style="width: 220px; text-indent: 10px"
                                value="Учебник Кыргызского языка для 7-го класса"
                            />
                        </div>
                        <a
                            href="#"
                            class="btn btn-primary btn-sm save"
                            style="margin: 9px 0px 0 10px;"
                        >Сохранить</a>
                        <a
                            id="download-link"
                            class="btn btn-primary btn-sm"
                            href="/books/uchebnik-kirgizskogo-yazika-dlya-7-go-klassa.etb"
                            style=" display: none; margin-top: 9px">Скачать учебник</a>
                    </div>
                </div>
                <div class="desktop"></div>
                <div class="display"></div>
            </div>
        </div>
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <script src="js/lib/jquery-2.1.1.min.js"></script>
        <script src="js/lib/bootstrap.min.js"></script>
        <script src="js/lib/underscore-min.js"></script>
        <script src="js/lib/backbone-min.js"></script>
        <script src="js/app.js"></script>
        <script src="js/eTextBook/utils.js"></script>
        <script src="js/eTextBook/editor.js"></script>
        <script src="js/eTextBook/module.js"></script>
        <script src="js/eTextBook/block.js"></script>
        <script src="js/eTextBook/template.js"></script>
        <script src="js/eTextBook/templates/module.js"></script>
        <script src="js/eTextBook/templates/block.js"></script>
        <script src="js/eTextBook/widget/inlineEdit.js"></script>
        <script src="js/eTextBook/widget/inlineEditInput.js"></script>
        <script src="js/eTextBook/widget/inlineEditTextarea.js"></script>
    </body>
</html>
