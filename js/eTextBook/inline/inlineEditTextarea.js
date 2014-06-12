var inlineEditTextarea = inlineEdit.extend({

    addWidget: function() {
        this.editCont = $('<edit-element class="' + this.get('editElementClass') + '"><textarea class="widget"></textarea></edit-element>');
        this.cont.append(this.editCont);
    }

    ,startEdit: function() {
        this.editCont.find('.widget').val(App.eTextBookUtils.parseTextBlockFromHtml(this.viewCont.html()));
    }

    ,finishEdit: function() {
        this.viewCont.html(App.eTextBookUtils.parseTextBlockToHtml(this.editCont.find('.widget').val()));
    }

});