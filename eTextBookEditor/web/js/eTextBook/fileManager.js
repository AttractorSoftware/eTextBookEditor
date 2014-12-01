var FileManager = function(cont) {

    var $this = this;

    this.cont = $(cont);
    this.uploadAction = this.cont.attr('upload-action');

    this.uploadCont = this.cont.find('.upload-container');

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
        this.uploadCont.find('input').bind('change', this.uploadFile);
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

        var input = $this.uploadCont.find('input');

        $this.uploadCont
            .find('label')
            .text('Файл загружается...')
            .prepend('<span class="glyphicon glyphicon-send"></span>');

        $this.createForm();
        $this.createIFrame();
        $this.form.append(input);
        $this.form.trigger('submit');
    }

    this.createForm = function() {
        this.form = $(
            '<form enctype="multipart/form-data" method="post" action="' + this.uploadAction + '" target="upload-iframe">' +
                '<input type="hidden" name="slug" value="' + this.slug + '" />' +
            '</form>'
        );
        $('body').append(this.form);
    }

    this.createIFrame = function() {
        this.iframe = $('<iframe id="upload-iframe" name="upload-iframe"></iframe>');
        $('body').append(this.iframe);
        this.iframe.bind('load', function() {
            $this.isLoaded($(this).contents().find('body').text().split('||'));
        });
        return this.iframe;
    }

    this.isLoaded = function(response) {
        $this.addFile(response);
        $this.iframe.remove();
        $this.form.remove();

        $this.uploadCont
            .find('label')
            .text('Выберите файл')
            .prepend('<span class="glyphicon glyphicon-folder-open"></span>');
        $this.uploadCont.append('<input type="file" name="upload-file" id="uploadInput" />');
        $this.uploadCont.find('input').bind('change', this.uploadFile);
    }

    this.addFile = function(response) {
        response = JSON.parse(response);
        var file = response.data.name.split('.');
        this.cont.find('.tab-content .' + response.data.type + ' .list').append(
            '<div title="' + response.data.name + '" data-placement="bottom" data-toggle="tooltip" class="item ' + file[1] + '">' + file[0] + '</div>'
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
        if($.isFunction(this.audioPlayer.find('audio')[0].load)){
            this.audioPlayer.find('audio')[0].load();
        }
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

App.fileManager = new FileManager($('.file-manager'))

