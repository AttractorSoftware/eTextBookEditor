    var eTextBookWidgetAudio = eTextBookWidget.extend({
    defaults: {
        slug: "audio"
        ,title: "Аудио записи"
        ,templateName: 'audioWidget'
        ,ico: '<span class="glyphicon glyphicon-music"></span>'
    }

    ,finishEdit: function() {
        for(var i = 0; i < this.editCont.find('audio-list audio-item').length; i++) {
            var item = $(this.editCont.find('audio-list audio-item')[i]);
            item.find('view-element.description').html(
                item.find('edit-element.description textarea').code(                )
            );
        }
    }

    ,startEdit: function() {
        for(var i = 0; i < this.editCont.find('audio-list audio-item').length; i++) {
            var item = $(this.editCont.find('audio-list audio-item')[i]);
            item.find('edit-element.description textarea').code(
                item.find('view-element.description').html()
            );
        }
    }

    ,activate: function() {
        var $this = this;
        this.editCont.find('audio-list').append('<edit-element class="add-audio"><span class="glyphicon glyphicon-music"></span>Добавить аудио запись</edit-element>');
        this.editCont.find('audio-list audio-item').append(
            '<edit-element class="glyphicon glyphicon-remove"></edit-element>' +
            '<edit-element class="description">' +
                '<label>Описание аудио файла</label>' +
                '<textarea></textarea>' +
            '</edit-element>'
        );
        this.editCont.find('textarea').summernote(App.eTextBookEditor.toolbarConfig);
        this.bindRemoveEvent();
        this.editCont.find('.add-audio').bind('click', function() {
            App.fileManager.pickFile(function(path) {
                $this.editCont.find('audio-list .add-audio').before(
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