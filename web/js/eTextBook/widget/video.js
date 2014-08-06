var eTextBookWidgetVideo = eTextBookWidget.extend({
    defaults: {
        slug: "video"
        ,title: "Видео записи"
        ,templateName: 'videoWidget'
        ,ico: '<span class="glyphicon glyphicon-facetime-video"></span>'
    }

    ,finishEdit: function() {
        this.editCont.find('video-description view-element').html(
            App.eTextBookUtils.parseTextBlockToHtml(this.editCont.find('video-description edit-element textarea').val())
        );
    }

    ,startEdit: function() {
        this.editCont.find('video-description edit-element textarea').val(
            App.eTextBookUtils.parseTextBlockFromHtml(this.editCont.find('video-description view-element').html())
        );
    }

    ,activate: function() {
        var $this = this;
        this.editCont.find('video-list').append('<edit-element class="add-video"><span class="glyphicon glyphicon-facetime-video"></span> Добавить видео</edit-element>');
        this.editCont.find('video-description').append('<edit-element><label> Вопросы или описание к видео записям:</label><textarea></textarea></edit-element>');
        this.editCont.find('video-list video-item').append('<edit-element class="glyphicon glyphicon-remove"></edit-element>');
        $this.bindRemoveEvent();
        this.editCont.find('.add-video').bind('click', function() {
            App.fileManager.pickFile(function(path) {
                var video = $(App.eTextBookTemplate.getTemplateWithParams('videoItem')({ path: path }));
                video.find('video').removeAttr('preload');
                $this.editCont.find('video-list').prepend(video);
                $this.bindRemoveEvent();
            });
        });
    }

    ,bindRemoveEvent: function() {
        this.editCont.find('video-list video-item .glyphicon-remove').unbind('click');
        this.editCont.find('video-list video-item .glyphicon-remove').bind('click', function() {
            $(this).parent().remove();
        });
    }

    ,viewActivate: function() {

    }
});

App.eTextBookWidgetRepository.registerWidget(eTextBookWidgetVideo);