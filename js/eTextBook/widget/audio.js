var eTextBookWidgetAudio = eTextBookWidget.extend({
    defaults: {
        slug: "audio"
        ,title: "Аудирование"
        ,templateName: 'audioWidget'
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
        this.editCont.find('audio-list').append('<edit-element class="add-audio"><span class="glyphicon glyphicon-music"></span></edit-element>');
        this.editCont.find('audio-list audio-item').append('<edit-element class="glyphicon glyphicon-remove"></edit-element>');
        this.editCont.find('audio-description').append('<edit-element><textarea></textarea></edit-element>');
        this.bindRemoveEvent();
        this.editCont.find('.add-audio').bind('click', function() {
            App.fileManager.pickFile(function(path) {
                $this.editCont.find('audio-list').prepend(
                    '<audio-item>' +
                        '<video controls><source src="' + path + '" type="audio/mpeg"></video>' +
                        '<edit-element class="glyphicon glyphicon-remove"></edit-element>' +
                    '</audio-item>'
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