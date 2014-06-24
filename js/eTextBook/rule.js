var eTextBookRule = Backbone.Model.extend({
    initialize: function() {
        this.cont = this.get('cont');
        this.editElements = {};
        this.editable = false;
        this.addControlPanel();
        this.addEditElements();
    }

    ,addControlPanel: function() {

        var $this = this;

        this.get('cont').prepend(
            '<control-panel>' +
                '<item class="edit" title="Редактировать"><span class="glyphicon glyphicon-pencil"></span></item>' +
                '<item class="duplicate" title="Дублировать"><span class="glyphicon glyphicon-repeat"></span></item>' +
                '<item class="remove" title="Удалить"><span class="glyphicon glyphicon-trash"></span></item>' +
                '</control-panel>'
        );

        this.get('cont').append(this.get('module').generateAddBlockButton());

        this.get('cont').find('control-panel .edit').bind('click', function() {
            if(!$this.editable) {
                $this.startEdit();
            } else { $this.finishEdit(); }
        });

        this.get('cont').find('control-panel .remove').bind('click', function() {
            $this.get('cont').remove();
            $this = null;
            App.eTextBookEditor.updateDisplay();
        });
    }

    ,startEdit: function() {
        this.get('cont').attr('editable', 1);
        this.editable = true;
        for(var i in this.editElements) {
            this.editElements[i].startEdit();
        }
    }

    ,finishEdit: function() {
        this.get('cont').attr('editable', 0);
        this.editable = false;
        for(var i = 0 in this.editElements) {
            this.editElements[i].finishEdit();
        }
        App.eTextBookEditor.updateDisplay();
    }

    ,addEditElements: function() {
        this.appendEditElement('title', new inlineEditTextarea({
            cont: this.cont.find('rule-title')
        }));
    }

    ,appendEditElement: function(title, element) {
        this.editElements[title] = element;
    }
});
