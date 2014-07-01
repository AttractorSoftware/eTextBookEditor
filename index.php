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
                    <div class="tab-pane video" id="videos">
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
                        <div class="player">
                            <video controls></video>
                            <div class="buttons">
                                <div class="btn btn-primary btn-sm select"> <span class="glyphicon glyphicon-ok"></span> Выбрать</div>
                                <div class="btn btn-danger btn-sm remove"> <span class="glyphicon glyphicon-remove"></span> Удалить</div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane audio" id="audios">
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
                        <div class="player">
                            <audio controls>
                                Your browser does not support the audio element.
                            </audio>
                            <div class="buttons">
                                <div class="btn btn-primary btn-sm select"> <span class="glyphicon glyphicon-ok"></span> Выбрать</div>
                                <div class="btn btn-danger btn-sm remove"> <span class="glyphicon glyphicon-remove"></span> Удалить</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <link rel="stylesheet" type="text/css" href="css/main-style.min.css" />
        <script src="js/script.min.js"></script>
    </body>
</html>
