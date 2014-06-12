var eTextBookWidgetQuestion = eTextBookWidget.extend({
    defaults: {
        slug: "question"
        ,title: "Вопрос"
        ,templateName: 'questionWidget'
    }

    ,finishEdit: function() {
        this.editCont.find('view-element').html(App.eTextBookUtils.parseTextBlockToHtml(this.editCont.find('textarea').val()));
    }

    ,startEdit: function() {
        this.editCont.find('textarea').val(App.eTextBookUtils.parseTextBlockFromHtml(this.editCont.find('view-element').html()));
    }

    ,activate: function() {
        this.editCont.append('<edit-element><textarea></textarea></edit-element>');
    }
});

App.eTextBookWidgetRepository.registerWidget(eTextBookWidgetQuestion);