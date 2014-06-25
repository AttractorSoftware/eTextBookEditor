<?php

    require_once "lib/eTextBook.class.php";
    require_once "lib/Util.class.php";

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
                                style="width: 330px; text-indent: 10px"
                                placeholder="Название учебника"
                                value="<?php echo isset($viewBook) ? $viewBook->getTitle() : ''; ?>"
                            />
                        </div>
                        <a
                            href="#"
                            class="btn btn-primary btn-sm save"
                            style="margin: 9px 0px 0 10px;"
                        > <span class="glyphicon glyphicon-floppy-save"></span>Сохранить</a>
                        <?php if(isset($viewBook)): ?>
                            <a
                                id="download-link"
                                class="btn btn-primary btn-sm"
                                href="/books/<?php echo $viewBook->getSlug(); ?>.etb"
                                style="margin-top: 9px">Скачать учебник</a>
                        <?php endif; ?>
                        <?php if(count($books)): ?>
                            <select id="book-list" style="width: 300px;">
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

        <div
            class="file-manager"
            <?php if(isset($viewBook)): ?>
                image-path="/tmp/<?php echo $viewBook->getSlug(); ?>/content/img"
                video-path="/tmp/<?php echo $viewBook->getSlug(); ?>/content/video"
                audio-path="/tmp/<?php echo $viewBook->getSlug(); ?>/content/audio"
                slug="<?php echo $viewBook->getSlug(); ?>"
            <?php endif; ?>
        >
            <div class="window">
                <div class="close glyphicon glyphicon-remove-circle"></div>
                <!-- Nav tabs -->
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#images" data-toggle="tab">Картинки</a></li>
                    <li><a href="#videos" data-toggle="tab">Видео</a></li>
                    <li><a href="#audios" data-toggle="tab">Аудио</a></li>

                    <div class="form-group">
                        <label for="uploadInput">
                            <span class="glyphicon glyphicon-folder-open"></span>
                            Загрузить файл
                        </label>
                        <input type="file" id="uploadInput">
                    </div>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane active image" id="images">
                        <div class="list">
                            <?php if(isset($viewBook)): ?>
                                <?php foreach($viewBook->getImages() as $img): ?>
                                    <div
                                        class="item <?php echo $img['extension']; ?>"
                                        data-toggle="tooltip"
                                        data-placement="bottom"
                                        title="<?php echo $img['title'] . "." . $img['extension'] ; ?>"
                                    >
                                        <?php echo $img['title']; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="player">
                            <div class="display"></div>
                            <div class="buttons">
                                <div class="btn btn-primary btn-sm select"> <span class="glyphicon glyphicon-ok"></span> Выбрать</div>
                                <div class="btn btn-danger btn-sm remove"> <span class="glyphicon glyphicon-remove"></span> Удалить</div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="videos">
                        <div class="list">
                            <?php if(isset($viewBook)): ?>
                                <?php foreach($viewBook->getVideos() as $video): ?>
                                    <div
                                        class="item <?php echo $video['extension']; ?>"
                                        data-toggle="tooltip"
                                        data-placement="bottom"
                                        title="<?php echo $video['title'] . "." . $video['extension'] ; ?>"
                                        >
                                        <?php echo $video['title']; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="player"></div>
                    </div>
                    <div class="tab-pane" id="audios">
                        <div class="list">
                            <?php if(isset($viewBook)): ?>
                                <?php foreach($viewBook->getAudios() as $audio): ?>
                                    <div
                                        class="item <?php echo $audio['extension']; ?>"
                                        data-toggle="tooltip"
                                        data-placement="bottom"
                                        title="<?php echo $audio['title'] . "." . $audio['extension'] ; ?>"
                                        >
                                        <?php echo $audio['title']; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="player"></div>
                    </div>
                </div>

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
        <script src="js/eTextBook/rule.js"></script>
        <script src="js/eTextBook/template.js"></script>
        <script src="js/eTextBook/templates/module.js"></script>
        <script src="js/eTextBook/templates/block.js"></script>
        <script src="js/eTextBook/templates/questionWidget.js"></script>
        <script src="js/eTextBook/templates/translateComparativeWidget.js"></script>
        <script src="js/eTextBook/templates/imageDescriptionWidget.js"></script>
        <script src="js/eTextBook/inline/inlineEdit.js"></script>
        <script src="js/eTextBook/inline/inlineEditInput.js"></script>
        <script src="js/eTextBook/inline/inlineEditTextarea.js"></script>
        <script src="js/eTextBook/widgetRepository.js"></script>
        <script src="js/eTextBook/widget.js"></script>
        <script src="js/eTextBook/widget/test.js"></script>
        <script src="js/eTextBook/widget/question.js"></script>
        <script src="js/eTextBook/widget/translateComparative.js"></script>
        <script src="js/eTextBook/widget/imageDescription.js"></script>
        <script src="js/eTextBook/builder.js"></script>
        <script src="js/eTextBook/fileManager.js"></script>
    </body>
</html>
