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
        this.editCont.find('image-description images').append('<edit-element class="add-image"><span class="glyphicon glyphicon-picture"></span></edit-element>');
        this.editCont.find('image-description images item').append('<edit-element class="glyphicon glyphicon-remove"></edit-element>');
    }
});

App.eTextBookWidgetRepository.registerWidget(eTextBookWidgetImageDescription);