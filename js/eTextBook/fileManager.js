var FileManager = function(cont) {

    var $this = this;

    this.cont = $(cont);

    this.uploadInput = this.cont.find('#uploadInput');

    this.imagePath = this.cont.attr('image-path');
    this.videoPath = this.cont.attr('video-path');
    this.audioPath = this.cont.attr('audio-path');

    this.imageList = this.cont.find('#images .list');
    this.imagePlayer = this.cont.find('#images .player');

    this.videoList = this.cont.find('#videos .list');
    this.videoPlayer = this.cont.find('#videos .player');

    this.audioList = this.cont.find('#audios .list');
    this.audioPlayer = this.cont.find('#audios .player');

    this.images = this.imageList.find('.item');
    this.videos = this.videoList.find('.item');
    this.audios = this.audioList.find('.item');

    this.init = function() {
        this.cont.find('.close').bind('click', $this.close);
        this.images.bind('click', function() { $this.setViewImage(this); });
        this.uploadInput.bind('change', this.uploadFile);
        this.show();
    }

    this.uploadFile = function() {
        $this.uploadInput.parent()
            .find('label')
            .text('Файл загружается...')
            .prepend('<span class="glyphicon glyphicon-send"></span>');
    }

    this.setViewImage = function(img) {
        this.imagePlayer.find('.display').css('backgroundImage', 'url(' + this.imagePath + '/' + $(img).attr('title') + ')')
        this.images.removeClass('selected');
        $(img).addClass('selected');
    }

    this.close = function() {
        $this.cont.hide();
    }

    this.show = function() {
        this.cont.show();
    }

    this.init();
}

App.fileManager = new FileManager($('.file-manager'));