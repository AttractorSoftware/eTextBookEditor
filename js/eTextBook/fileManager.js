var FileManager = function(cont) {

    var $this = this;

    this.cont = $(cont);

    this.uploadInput = this.cont.find('#uploadInput');

    this.slug = this.cont.attr('slug');

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
        this.activateImages();
        this.activateAudios();
        this.activateVideos();
        this.uploadInput.bind('change', this.uploadFile);
        this.imagePlayer.find('.buttons .select').bind('click', function() {
            $this.afterPick($this.viewImagePath);
            $this.close();
        });
        this.audioPlayer.find('.buttons .select').bind('click', function() {
            $this.afterPick($this.viewAudioPath);
            $this.close();
        });

        this.videoPlayer.find('.buttons .select').bind('click', function() {
            $this.afterPick($this.viewVideoPath);
            $this.close();
        });

        this.imagePlayer.find('.buttons .remove').bind('click', function() {
            $.post('/remove-file.php', { file: $this.viewImagePath });
        });

        this.audioPlayer.find('.buttons .remove').bind('click', function() {
            $.post('/remove-file.php', { file: $this.viewAudioPath });
        });

        this.videoPlayer.find('.buttons .remove').bind('click', function() {
            $.post('/remove-file.php', { file: $this.viewVideoPath });
        });
    }

    this.activateImages = function() {
        this.imageList.find('.item').unbind('click');
        this.imageList.find('.item').bind('click', function() { $this.setViewImage(this); });
    }

    this.activateAudios = function() {
        this.audioList.find('.item').unbind('click');
        this.audioList.find('.item').bind('click', function() { $this.setViewAudio(this); });
    }

    this.activateVideos = function() {
        this.videoList.find('.item').unbind('click');
        this.videoList.find('.item').bind('click', function() { $this.setViewVideo(this); });
    }

    this.uploadFile = function() {
        $this.uploadInput.parent()
            .find('label')
            .text('Файл загружается...')
            .prepend('<span class="glyphicon glyphicon-send"></span>');

        $this.createForm();
        $this.createIFrame();
        $this.form.append($this.uploadInput);
        $this.uploadInput.attr('name', 'upload-file');
        $this.form.trigger('submit');
    }

    this.createForm = function() {
        this.form = $(
            '<form enctype="multipart/form-data" method="post" action="/upload-file.php" target="upload-iframe">' +
                '<input type="hidden" name="slug" value="' + this.slug + '" />' +
            '</form>'
        );
        $('body').append(this.form);
    }

    this.createIFrame = function() {
        this.iframe = $('<iframe id="upload-iframe" name="upload-iframe"></iframe>');
        $('body').append(this.iframe);
        this.iframe.bind('load', function() {
           var response = $(this).contents().find('body').html().split('||');
           $this.addFile(response);
        });
        return this.iframe;
    }

    this.addFile = function(response) {

        var file = response[1].split('.');

        this.cont.find('.tab-content .' + response[0] + ' .list').append(
            '<div title="' + response[1] + '" data-placement="bottom" data-toggle="tooltip" class="item ' + file[1] + '">' + file[0] + '</div>'
        );
        this.activateImages();
        this.activateAudios();
        this.activateVideos();
    }

    this.setViewImage = function(img) {
        this.viewImagePath = this.imagePath + '/' + $(img).attr('title');
        this.imagePlayer.find('.display').css('backgroundImage', 'url(' + this.imagePath + '/' + $(img).attr('title') + ')')
        this.images.removeClass('selected');
        $(img).addClass('selected');
    }

    this.setViewAudio = function(audio) {
        this.viewAudioPath = this.audioPath + '/' + $(audio).attr('title');
        this.audioPlayer.find('audio').html('');
        this.audioPlayer.find('audio').append('<source src="' + this.viewAudioPath + '" type="audio/mpeg">');
        this.audioPlayer.find('audio')[0].load();
        this.audios.removeClass('selected');
        $(audio).addClass('selected');
    }

    this.setViewVideo = function(video) {
        this.viewVideoPath = this.videoPath + '/' + $(video).attr('title');
        this.videoPlayer.find('video').html('');
        this.videoPlayer.find('video').append('<source src="' + this.viewVideoPath + '" type="video/mp4">');
        this.videoPlayer.find('video')[0].load();
        this.videos.removeClass('selected');
        $(video).addClass('selected');
    }

    this.close = function() {
        $this.cont.hide();
    }

    this.show = function() {
        this.cont.show();
    }

    this.pickFile = function(afterPick) {
        this.afterPick = afterPick;
        this.show();
    }

    this.init();
}

App.fileManager = new FileManager($('.file-manager'));