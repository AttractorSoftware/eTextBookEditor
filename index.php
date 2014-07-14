<?php

    require_once "lib/eTextBook.class.php";

    $bookContent = "";
    $bookTitle = "";
    if(isset($_GET['book'])) {
        $viewBook = new eTextBook($_GET['book']);
    }
    $books = Util::fileList(Util::getRootDir().'books');

?>

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
                                style="width: 230px; text-indent: 10px"
                                placeholder="Название учебника"
                                value="<?php echo isset($viewBook) ? $viewBook->getTitle() : ''; ?>"
                            />
                        </div>
                        <a
                            href="#"
                            class="btn btn-primary btn-sm save"
                            style="margin: 9px 0px 0 10px;"
                            id="save-book-btn"
                        > <span class="glyphicon glyphicon-floppy-save"></span>Сохранить</a>
                        <?php if(isset($viewBook)): ?>
                            <a
                                id="download-link"
                                class="btn btn-primary btn-sm"
                                href="/books/<?php echo $viewBook->getSlug(); ?>.etb"
                                style="margin-top: 9px">Скачать учебник</a>
                        <?php endif; ?>
                        <?php if(count($books)): ?>
                            <select id="book-list" style="width: 200px;">
                                <option value="">Новый учебник</option>
                                <?php foreach($books as $book): ?>
                                    <option
                                        value="<?php echo $book; ?>"
                                        <?php echo isset($viewBook) && $viewBook->getSlug() . ".etb" == $book ? 'selected' : ''; ?>
                                    >
                                        <?php echo $book; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                        <a href="/ebook.apk" style="float: right; margin: 12px 10px 0 0">Андройд ридер</a>
                    </div>
                </div>
                <div class="desktop"><?php echo isset($viewBook) ? $viewBook->getContent() : ''; ?></div>
                <div class="display e-text-book-viewer"></div>
            </div>
        </div>

        <?php require_once 'fileManager.php'; ?>
        <?php require_once 'jsTemplates.php'; ?>

        <link rel="stylesheet" type="text/css" href="css/main-style.min.css" />
        <script src="js/script.min.js"></script>
    </body>
</html>