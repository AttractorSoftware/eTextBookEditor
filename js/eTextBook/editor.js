var eTextBookEditor = Backbone.Model.extend({

    initialize: function() {
        this.desktop = this.get('cont').find('.desktop');
        this.display = this.get('cont').find('.display');
        this.root = this.getRoot();
        this.modules = [];
        this.root.prepend(this.generateAddModuleButton());
        this.updateDisplay();
    }

    ,collectModules: function() {
        for(var i = 0; i < this.root.find('module').length; i++) {
            this.modules.push(new eTextBookModule({
                cont: $(this.root.find('module')[i])
            }));
        }
    }

    ,getRoot: function() {
        var root = this.desktop.find('e-text-book');
        if(!root.length) {
            this.desktop.html('');
            this.desktop.append('<e-text-book />');
            root = this.desktop.find('e-text-book');
        } return root;
    }

    ,generateAddModuleButton: function() {
        var $this = this;
        var button = $('<add-module-button><wrap><a href="#" class="add-module">Добавить модуль</a></wrap></add-module-button>');

        button.bind('click', function() {
            if($(this).hasClass('open')){
                $(this).removeClass('open');
            } else { $(this).addClass('open'); }
        });

        button.find('a.add-module').bind('click', function() {
            $this.addModule(button);
        });

        return button;
    }

    ,addModule: function(button) {
        var template = $(App.eTextBookTemplate.getTemplate('module'));

        if(button.parent().prop('tagName') == 'MODULE') {
            button.parent().after(template);
        } else {
            $(button).after(template);
        }

        var module = new eTextBookModule({ cont: $(template) });
        this.modules.push(module);
        module.startEdit();
    }

    ,updateDisplay: function() {
        var book = $(this.desktop.html());

        book = this.clearEditElements(book);
        book = this.setIndexes(book);

        this.display.html('');
        this.display.append(book);
    }

    ,clearEditElements: function(html) {
        html.find('edit-element, add-module-button, add-block-button, control-panel').remove();
        return html;
    }

    ,setIndexes: function(html) {
        var modules = html.find('module');
        for(var i = 0; i < modules.length; i++) {
            var module = $(modules[i]);
            module.attr('moduleId', i + 1);
            for(var j = 0; j < module.find('block').length; j++) {
                var block = $(module.find('block')[j]);
                block.attr('blockId', j + 1);
                block.find('block-index').html(j + 1 + '.');
            }
        }
        return html;
    }

});

$(function() {
   App.eTextBookEditor = new eTextBookEditor({
       cont: $('.e-text-book-editor')
   });
   App.eTextBookEditor.collectModules();
});
