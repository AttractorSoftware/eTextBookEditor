var eTextBookEditor = Backbone.Model.extend({

    initialize: function() {
        var $this = this;
        this.desktop = this.get('cont').find('.desktop');
        this.display = this.get('cont').find('.display');
        this.root = this.getRoot();
        this.modules = [];
        this.toolbarConfig = {
            height: 300
            ,toolbar: [
                ['style', ['style', 'bold', 'italic', 'underline', 'strikethrough']]
                ,['layout', ['ol', 'ul', 'paragraph', 'heihgt']]
                ,['insert', ['table']]
                ,['misc', ['fullscreen', 'undo', 'redo', 'codeview']]
            ]
        };
        this.clearViewElements(this.desktop);
        this.updateDisplay(true);
        this.synchronizeScrolls();

        $('.e-text-book-editor').css({ height: $(window).height() *.75 });

        if(this.get('cont').hasClass('view-mode')) {
            this.display = $('.e-text-book-viewer');
            this.activateDisplayWidgets();
        }
    }

    ,synchronizeScrolls: function() {

        var $this = this;

        this.desktop.bind('scroll', function(e) {
            var scrollTop = $this.desktop.prop('scrollTop') * $this.display.prop('scrollHeight') / $this.desktop.prop('scrollHeight')
            $this.display.prop('scrollTop', scrollTop);
        });
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
        var button = $(App.eTextBookTemplate.getTemplate('addModuleButton'));

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
        template.attr('id', App.eTextBookUtils.generateUID());

        if(button.parent().prop('tagName') == 'MODULE') {
            button.parent().after(template);
        } else {
            $(button).after(template);
        }

        var module = new eTextBookModule({ cont: $(template) });
        this.modules.push(module);
        module.startEdit();
    }

    ,updateDisplay: function(missSave) {
        var book = $(this.desktop.html());

        book = this.clearEditElements(book);
        book = this.setIndexes(book);

        this.display.html('');
        this.display.append(book);
        this.activateDisplayWidgets();
        if(!missSave) {
            this.save();
        }

    }

    ,activateDisplayWidgets: function() {
        for(var i = 0; i < this.display.find('widget').length; i++) {
            var widgetCont = $(this.display.find('widget')[i]);
            if(widgetCont.attr('widget-slug')) {
                var widget = App.eTextBookWidgetRepository.getWidgetBySlug(widgetCont.attr('widget-slug'));
                widget = new widget();
                widget.contentContainer = widgetCont.find('widget-content');
                widget.viewActivate();
            }
        }
    }

    ,save: function() {
        var $this = this;
        $.post(this.get('cont').attr('update-action'), {
            book: this.get('cont').attr('book')
            ,module: this.get('cont').attr('module')
            ,content: this.getContent()
        }, function(response) {
            $this.updateDisplay(true);
        });
    }

    ,clearEditElements: function(html) {
        html.find(
            'edit-element,' +
            'add-module-button,' +
            'add-block-button,' +
            'control-panel,' +
            '.widget-selector,' +
            '.html5-controls'
        ).remove();
        return html;
    }

    ,clearViewElements: function(html) {
        html.find('block-index').html('');
        html.find('.html5-controls').remove();
        return html;
    }

    ,getContent: function() {
        var saveData = this.display;
        saveData.find('.not-saved').remove();
        return saveData.html();
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
