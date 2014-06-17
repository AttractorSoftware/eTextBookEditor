var eTextBookWidgetImageDescription = eTextBookWidget.extend({
    defaults: {
        slug: "image-description"
        ,title: "Описание картинки"
        ,templateName: 'imageDescription'
    }

    ,finishEdit: function() {

    }

    ,startEdit: function() {

    }

    ,activate: function() {
        var $this = this;
        this.editCont.find('image-description images').append('<edit-element class="add-image"><span class="glyphicon glyphicon-picture"></span></edit-element>');
        this.editCont.find('image-description images item').append('<edit-element class="glyphicon glyphicon-remove remove"></edit-element>');
        this.bindRemoveEvent();
        this.editCont.find('.add-image').bind('click', function() {
            App.fileManager.pickFile(function(path){
                $this.editCont.find('.add-image').before('<item style="background-image: url(' + path + ')"><edit-element class="glyphicon glyphicon-remove"></edit-element></item>');
                $this.bindRemoveEvent();
            });
        });
    }

    ,bindRemoveEvent: function() {
        this.editCont.find('images item .remove').unbind('click');
        this.editCont.find('images item .remove').bind('click', function() {
            $(this).parent().remove();
        });
    }

    ,addImage: function(path) {

    }

    ,viewActivate: function() {

    }
});

App.eTextBookWidgetRepository.registerWidget(eTextBookWidgetImageDescription);