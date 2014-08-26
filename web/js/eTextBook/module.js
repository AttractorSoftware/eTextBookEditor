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
        for(i = 0; i < this.get('cont').find('blocks rule').length; i++) {
            this.blocks.push(new eTextBookRule({
                cont: $(this.get('cont').find('blocks rule')[i])
                ,module: this
            }));
        }

        for(i = 0; i < this.get('cont').find('blocks .block.header').length; i++) {
            this.blocks.push(new eTextBookHeader({
                cont: $(this.get('cont').find('blocks .block.header')[i])
                ,module: this
            }));
        }
    }

    ,addControlPanel: function() {

        var $this = this;

        this.get('cont').prepend(App.eTextBookTemplate.getTemplate('moduleControlPanel'));

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
            App.eTextBookEditor.updateDisplay();
        });
    }

    ,duplicate: function() {
        var html = $(this.get('cont').html());
        html = App.eTextBookEditor.clearEditElements(html);
        var module = $('<module></module>');
        module.append(html);
        App.eTextBookEditor.root.append(module);
        App.eTextBookEditor.modules.append(new eTextBookModule({ cont: module }));
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
        var button = $(App.eTextBookTemplate.getTemplate('addBlockButton'));

        button.bind('click', function() {
            if($(this).hasClass('open')){
                $(this).removeClass('open');
            } else { $(this).addClass('open'); }
        });

        button.find('.add-block').popover();
        button.find('.add-block').bind('click', function() {
            $this.addBlock(button);
            $(this).parent().parent().removeClass('open');
            return false;
        });

        button.find('.add-rule').popover();
        button.find('.add-rule').bind('click', function() {
            $this.addRule(button);
            $(this).parent().parent().removeClass('open');
            return false;
        });

        button.find('.add-header').bind('click', function() {
            $this.addHeader(button);
            $(this).parent().parent().removeClass('open');
            return false;
        });

        return button;
    }

    ,addHeader: function(button) {
        var template = $('<div class="header block">' +
            '<div class="header-title">' +
                '<view-element></view-element>' +
            '</div>' +
        '</div>');

        template.attr('id', App.eTextBookUtils.generateUID());

        if(button.parent().prop('tagName') == 'BLOCK' || button.parent().prop('tagName') == 'RULE' || button.parent().prop('tagName') == 'HEADER') {
            button.parent().after(template);
        } else {
            $(button).parent().find('blocks').prepend(template);
        }

        var header = new eTextBookHeader({ cont: template, module: this });
        header.startEdit();
    }

    ,addRule: function(button) {
        var template = $('<rule>' +
            '<rule-title>' +
                '<view-element></view-element>' +
            '</rule-title>' +
        '</rule>');

        template.attr('id', App.eTextBookUtils.generateUID());

        if(button.parent().prop('tagName') == 'BLOCK' || button.parent().prop('tagName') == 'RULE' || button.parent().prop('tagName') == 'HEADER') {
            button.parent().after(template);
        } else {
            $(button).parent().find('blocks').prepend(template);
        }

        var rule = new eTextBookRule({ cont: template, module: this });
        rule.startEdit();
    }

    ,addBlock: function(button) {
        var template = $(App.eTextBookTemplate.getTemplate('block'));

        template.attr('id', App.eTextBookUtils.generateUID());

        if(button.parent().prop('tagName') == 'BLOCK' || button.parent().prop('tagName') == 'RULE' || button.parent().prop('tagName') == 'HEADER') {
            button.parent().after(template);
        } else {
            $(button).parent().find('blocks').prepend(template);
        }

        var block = new eTextBookBlock({ cont: $(template), module: this });
        this.blocks.push(block);
        block.startEdit();
    }
});