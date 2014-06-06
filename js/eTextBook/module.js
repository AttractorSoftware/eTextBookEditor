var eTextBookModule = Backbone.Model.extend({

    initialize: function() {
        this.cont = this.get('cont');
        this.blocks = [];
        this.editElements = [];
        this.editable = false;
        this.collectBlocks();
        this.addControlPanel();
        this.addEditElements();
    }

    ,collectBlocks: function() {
        for(var i = 0; i < this.get('cont').find('blocks block').length; i++) {
            this.blocks.push(new eTextBookBlock({
                cont: $(this.get('cont').find('blocks block')[i])
                ,module: this
            }));
        }
    }

    ,addControlPanel: function() {

        var $this = this;

        this.get('cont').prepend(
            '<control-panel class="module-panel">' +
                '<item class="edit" title="Редактировать"><span class="glyphicon glyphicon-pencil"></span></item>' +
                '<item class="duplicate" title="Дублировать"><span class="glyphicon glyphicon-repeat"></span></item>' +
                '<item class="remove" title="Удалить"><span class="glyphicon glyphicon-trash"></span></item>' +
            '</control-panel>'
        );

        this.get('cont').append(App.eTextBookEditor.generateAddModuleButton());
        this.get('cont').find('blocks').before(this.generateAddBlockButton());

        this.get('cont').find('control-panel.module-panel .edit').bind('click', function() {
            if(!$this.editable) {
                $this.startEdit();
            } else { $this.finishEdit(); }
        });

        this.get('cont').find('control-panel.module-panel .duplicate').bind('click', function() {
            $this.duplicate();
        });

        this.get('cont').find('control-panel.module-panel .remove').bind('click', function() {
            $this.get('cont').remove();
            $this = null;
        });
    }

    ,duplicate: function() {
        var html = App.eTextBookEditor.clearEditElements(this.get('cont').html());
    }

    ,startEdit: function() {
        this.get('cont').attr('editable', 1);
        this.editable = true;
        for(var i = 0; i < this.editElements.length; i++) {
            this.editElements[i].startEdit();
        }
    }

    ,finishEdit: function() {
        this.get('cont').attr('editable', 0);
        this.editable = false;
        for(var i = 0; i < this.editElements.length; i++) {
            this.editElements[i].finishEdit();
        }
        App.eTextBookEditor.updateDisplay();
    }

    ,addEditElements: function() {
        this.editElements.push(new inlineEditInput({
            cont: this.cont.find('module-title')
            ,editElementClass: 'module-element'
        }));

        this.editElements.push(new inlineEditTextarea({
            cont: this.cont.find('module-questions')
            ,editElementClass: 'module-element'
        }));

        this.editElements.push(new inlineEditTextarea({
            cont: this.cont.find('module-description')
            ,editElementClass: 'module-element'
        }));
    }

    ,generateAddBlockButton: function() {
        var $this = this;
        var button = $('<add-block-button><wrap><a href="#" class="add-block">Добавить блок</a></wrap></add-module-button>');

        button.bind('click', function() {
            if($(this).hasClass('open')){
                $(this).removeClass('open');
            } else { $(this).addClass('open'); }
        });

        button.find('a.add-block').bind('click', function() {
            $this.addBlock(button);
        });

        return button;
    }

    ,addBlock: function(button) {
        var template = $(App.eTextBookTemplate.getTemplate('block'));

        if(button.parent().prop('tagName') == 'BLOCK') {
            button.parent().after(template);
        } else {
            $(button).parent().find('blocks').prepend(template);
        }

        var block = new eTextBookBlock({ cont: $(template), module: this });
        this.blocks.push(block);
        block.startEdit();
    }
});