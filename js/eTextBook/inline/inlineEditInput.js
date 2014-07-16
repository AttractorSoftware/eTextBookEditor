var inlineEditInput = inlineEdit.extend({

    addWidget: function() {
        this.editCont = $('<edit-element class="'
            + this.get('editElementClass')
            + '"><label>Текст задания:</label><input type="text" class="widget"></edit-element>');
        this.cont.append(this.editCont);
    }

    ,startEdit: function() {
        this.editCont.find('.widget').val(this.viewCont.text());
    }

    ,finishEdit: function() {
        this.viewCont.html(this.editCont.find('.widget').val());
    }

});