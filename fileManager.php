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