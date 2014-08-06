var eTextBookWidgetAudio = eTextBookWidget.extend({
    defaults: {
        slug: "audio"
        ,title: "Аудио записи"
        ,templateName: 'audioWidget'
        ,ico: '<span class="glyphicon glyphicon-music"></span>'
    }

    ,finishEdit: function() {
        this.editCont.find('audio-description view-element').html(
            App.eTextBookUtils.parseTextBlockToHtml(this.editCont.find('audio-description edit-element textarea').val())
        );
    }

    ,startEdit: function() {
        this.editCont.find('audio-description edit-element textarea').val(
            App.eTextBookUtils.parseTextBlockFromHtml(this.editCont.find('audio-description view-element').html())
        );
    }

    ,activate: function() {
        var $this = this;
        this.editCont.find('audio-list').append('<edit-element class="add-audio"><span class="glyphicon glyphicon-music"></span>Добавить аудио запись</edit-element>');
        this.editCont.find('audio-list audio-item').append('<edit-element class="glyphicon glyphicon-remove"></edit-element>');
        this.editCont.find('audio-description').append('<edit-element><label>Вопросы или описание к аудио записям:</label><textarea></textarea></edit-element>');
        this.bindRemoveEvent();
        this.editCont.find('.add-audio').bind('click', function() {
            App.fileManager.pickFile(function(path) {
                $this.editCont.find('audio-list').prepend(
                    App.eTextBookTemplate.getTemplateWithParams('audioItem')({ path: path })
                );
                $this.bindRemoveEvent();
            });
        });
    }

    ,bindRemoveEvent: function() {
        this.editCont.find('audio-list audio-item .glyphicon-remove').unbind('click');
        this.editCont.find('audio-list audio-item .glyphicon-remove').bind('click', function() {
            $(this).parent().remove();
        });
    }

    ,viewActivate: function() {

    }
});

App.eTextBookWidgetRepository.registerWidget(eTextBookWidgetAudio);