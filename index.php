<?php
    $bookContent = "";
    $bookTitle = "";
    if(isset($_GET['book'])) {
        $viewBook = $_GET['book'];
        $bookSlug = explode('.', $viewBook);
        $bookSlug = $bookSlug[0];
        $zip = new ZipArchive();
        $archive = $zip->open("books/".$_GET['book']);
        if($archive === true) {
            $zip->extractTo('/tmp/ebook');
            $zip->close();
            $bookContent = file_get_contents('/tmp/ebook/' . $bookSlug . '/index.html');
            $bookContent = explode('<e-text-book>', $bookContent);
            $bookContent = explode('</e-text-book>', $bookContent[1]);
            $bookContent = $bookContent[0];
            $bookContent = "<e-text-book>" . $bookContent . "</e-text-book>";
            $bookInfo = file_get_contents('/tmp/ebook/' . $bookSlug . '/book.info');
            $bookInfo = explode('=+=', $bookInfo);
            $bookTitle = $bookInfo[1];
        } else {
            die('Archive not found');
        }
    }
    $books = array();
    foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator('books', FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
        $book = explode('/', $path);
        $books[] = $book[1];
    }

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
                                style="width: 330px; text-indent: 10px"
                                placeholder="Учебник Кыргызского языка для 7-го класса"
                                value="<?php echo $bookTitle; ?>"
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
                        <?php if(count($books)): ?>
                            <select id="book-list">
                                <option value="">Новый учебник</option>
                                <?php foreach($books as $book): ?>
                                    <option value="<?php echo $book; ?>" <?php echo $viewBook == $book ? 'selected' : ''; ?>><?php echo $book; ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="desktop"><?php echo $bookContent; ?></div>
                <div class="display e-text-book-viewer"></div>
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
        <script src="js/eTextBook/templates/questionWidget.js"></script>
        <script src="js/eTextBook/templates/translateComparativeWidget.js"></script>
        <script src="js/eTextBook/inline/inlineEdit.js"></script>
        <script src="js/eTextBook/inline/inlineEditInput.js"></script>
        <script src="js/eTextBook/inline/inlineEditTextarea.js"></script>
        <script src="js/eTextBook/widgetRepository.js"></script>
        <script src="js/eTextBook/widget.js"></script>
        <script src="js/eTextBook/widget/test.js"></script>
        <script src="js/eTextBook/widget/question.js"></script>
        <script src="js/eTextBook/widget/translateComparative.js"></script>
        <script src="js/eTextBook/builder.js"></script>
    </body>
</html>
