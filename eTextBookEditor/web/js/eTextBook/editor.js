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
                ,['insert', ['picture']]
            ]
            ,onpaste: function(e) {
                var thisNote = $(this);
                var updatePastedText = function(someNote){
                    var original = someNote.code();
                    var cleaned = $this.CleanPastedHTML(original);
                    someNote.code('').html(cleaned);
                };
                setTimeout(function () {
                    updatePastedText(thisNote);
                }, 10);
            }
        };
        this.clearViewElements(this.desktop);
        this.updateDisplay(true);
        this.synchronizeScrolls();
        this.advanceControlActivate();

        $('.e-text-book-editor').css({ height: $(window).height() *.75 });

        if(this.get('cont').hasClass('view-mode')) {
            this.display = $('.e-text-book-viewer');
            this.activateDisplayWidgets();
        }
    }

    ,advanceControlActivate: function() {
        var $this = this;
        var panel = $('#advance-control-panel');
        if(panel.length) {
            $('#clearMSEntities').bind('click', function(){
                $this.clearMSEntities();
            });
            $('#saveModule').bind('click', function() {
                $this.save();
                return false;
            });
            $('#convertInlineImages').bind('click', function() {
                $this.convertInlineImages();
                return false;
            });
        }
    }

    ,convertInlineImages: function() {
        var images = this.desktop.find('img');
        this.inlineImages = [];
        for(var i = 0; i < images.length; i++) {
            var image = $(images[i]);
            if(this.isInlineImage(image)) {
                this.inlineImages.push(image);
            }
        }
        if(this.inlineImages.length) {
            this.saveInlineImagesAsFiles();
        }

    }

    ,saveInlineImagesAsFiles: function() {
        var $this = this;
        var image = this.inlineImages[0];
        var extension = image.attr('src').split(';')[0].split('/')[1];
        $.post('/app_dev.php/save-inline-image', {
            bookSlug: this.get('cont').attr('book'),
            extension: extension,
            imageContent: image.attr('src')
        }, function(response) {
            if(response.status == 'ok') {
                image.attr('src', response.filePath);
                $this.inlineImages.splice(0, 1);
                if($this.inlineImages.length) {
                    $this.saveInlineImagesAsFiles();
                }
            }
        });
    }

    ,isInlineImage: function(image) {
        return image.attr('src').length > 1000;
    }

    ,clearMSEntities: function() {
        for(var i = 0; i < $('rule view-element, question view-element').length; i++) {
            var element = $($('rule-title view-element, question view-element')[i]);
            element.html(this.CleanPastedHTML(element.html()));
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

        this.convertInlineImages();

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
        this.showSaveNotify();
        $.post(this.get('cont').attr('update-action'), {
            book: this.get('cont').attr('book')
            ,module: this.get('cont').attr('module')
            ,content: this.getContent().replace(/\s+/g, ' ')
            ,blocks: this.collectIndexBlocks()
        }, function(response) {
            $this.hideSaveNotify();
            $this.updateDisplay(true);
        });
    }

    ,collectIndexBlocks: function() {
        var blocks = this.display.find('blocks block');
        var result = [];
        for(var i = 0; i < blocks.length; i++) {
            var block = $(blocks[i]);
            if(block.attr('index-disable') == 0) {
                result.push({
                    id: block.attr('id'),
                    title: block.find('block-title view-element').html()
                });
            }
        } return result;
    }

    ,showSaveNotify: function() {
        this.notifyInterval = setInterval(function(){
             if($('#save-notify').hasClass('show')) {
                 $('#save-notify').fadeOut(function(){ $('#save-notify').removeClass('show') });
             } else {
                 $('#save-notify').fadeIn(function(){ $('#save-notify').addClass('show') });
             }
        }, 1000);
    }

    ,hideSaveNotify: function() {
        clearInterval(this.notifyInterval);
        setTimeout(function(){
            $('#save-notify').fadeOut();
            $('#save-notify').removeClass('show');
        }, 1000);
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
            var blockIndex = 1;
            for(var j = 0; j < module.find('block').length; j++) {
                var block = $(module.find('block')[j]);
                if(block.attr('index-disable') != '1') {
                    block.attr('blockId', blockIndex);
                    block.find('block-index').html(blockIndex + '.');
                    blockIndex++;
                } else { block.find('block-index').html(''); }
                this.checkBlock(block);
            }
        }
        return html;
    }

    ,checkBlock: function(block) {
        var id = $(block).attr('id');
        if(!id) {
            $(block).attr('id', App.eTextBookUtils.generateUID());
        }
    }

    ,setAnimation: function(html) {
        var blocks = html.find('rule, block, div.header.block');
        for(var i = 0; i < blocks.length; i++) {
            var block = $(blocks[i]);
            switch(block.prop('localName')) { // Clear animation for old books
                case "block":  {
                    block.attr('data-anijs', "");
                    break;
                }
                case "rule": {
                    block.attr('data-anijs', "");
                    break;
                }
                default: {
                    block.attr('data-anijs', "");
                    break;
                }
            }
        } return html;
    }

    ,CleanPastedHTML: function(input) {
        var stringStripper = /(\n|\r| class=(")?Mso[a-zA-Z]+(")?)/g;
        var output = input.replace(stringStripper, ' ');
        var commentSripper = new RegExp('<!--(.*?)-->','g');
        var output = output.replace(commentSripper, '');
        var tagStripper = new RegExp('<(/)*(meta|link|span|\\?xml:|st1:|o:|font)(.*?)>','gi');
        output = output.replace(tagStripper, '');
        var badTags = ['style', 'script','applet','embed','noframes','noscript'];

        for (var i=0; i< badTags.length; i++) {
            tagStripper = new RegExp('<'+badTags[i]+'.*?'+badTags[i]+'(.*?)>', 'gi');
            output = output.replace(tagStripper, '');
        }
        var badAttributes = ['style', 'start'];
        for (var i=0; i< badAttributes.length; i++) {
            var attributeStripper = new RegExp(' ' + badAttributes[i] + '="(.*?)"','gi');
            output = output.replace(attributeStripper, '');
        }
        return output;
    }

});

$(function() {
   App.eTextBookEditor = new eTextBookEditor({
       cont: $('.e-text-book-editor')
   });
   App.eTextBookEditor.collectModules();
});
