var inlineEditInput = inlineEdit.extend({

    addWidget: function() {
        this.editCont = $('<edit-element class="'
            + this.get('editElementClass')
            + '"><label>' +
                Translator._('Заголовок задания') + ':' +
                '<div class="index-disable"><input type="checkbox">' + Translator._('Не индексировать') + '</div>' +
            '</label>' +
			  '<input type="text" class="widget">' +
			  '</edit-element>');
        this.cont.append(this.editCont);
    }

    ,startEdit: function() {
        this.editCont.find('.widget').val(this.viewCont.text());
        if(this.get('cont').parent().parent().attr('index-disable') == '1') {
            this.editCont.find('.index-disable input').prop('checked', true);
        } else {
            this.editCont.find('.index-disable input').prop('checked', false);
        }
    }

    ,finishEdit: function() {
        this.viewCont.html(this.editCont.find('.widget').val());
        if(this.get('cont').parent().parent().prop('tagName') != "E-TEXT-BOOK") {
            if(this.editCont.find('.index-disable input').prop('checked')) {
                this.get('cont').parent().parent().attr('index-disable', 1);
            } else {
                this.get('cont').parent().parent().attr('index-disable', 0);
            }
        }
    }

});