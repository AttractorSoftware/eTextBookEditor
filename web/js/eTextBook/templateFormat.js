var templateFormat = {

    setRootTag: function(tag){
        this.rootTag = tag;
    }

    ,parseData: function() {
        this.parseModuleData();
    }

    ,parseModuleData: function() {
        this.moduleTitle = this.rootTag.find('module-title view-element').html();
        this.moduleDescription = this.rootTag.find('module-description view-element').html();
        this.moduleQuestions = this.rootTag.find('module-questions view-element').html();
    }

    ,reDraw: function() {
        this.reDrawModuleData();
        this.reDrawHeaders();
        this.reDrawRules();
        this.reDrawTasks();
    }

    ,reDrawTasks: function() {
        var tasks = this.rootTag.find('block');
        for(var i = 0; i < tasks.length; i++) {
           var task = $(tasks[i]);
           var position = task.find('block-index').html();
           var title = task.find('block-title view-element').html();
           var content = task.find('block-content').html();
           task.addClass('row').addClass('block').addClass('.task').html(
               '<div class="col-sm-1 position">' + position + '</div>' +
               '<div class="col-sm-1 task-types">' +
                    '<div class="item"></div>' +
                    '<div class="item"></div>' +
               '</div>' +
               '<div class="col-sm-10 block-content">' +
                   '<div class="task-content">' +
                        '<div class="task-title">' + title + '</div>' +
                        '<div class="simple-text">' +
                            content +
                        '</div>' +
                   '</div>' +
               '</div>'
           );
        }
    }

    ,reDrawRules: function() {
        var rules = this.rootTag.find('rule');
        for(var i = 0; i < rules.length; i++) {
            var rule = $(rules[i]);
            var content = rule.find('view-element').html();
            rule.addClass('row').addClass('block').addClass('rule').html('');
            rule.html(
                '<div class="col-sm-2">&nbsp;</div>' +
                '<div class="col-sm-10 block-content">' +
                    '<div class="rule-content">' +
                        content +
                    '</div>' +
                '</div>'
            );
        }
    }

    ,reDrawHeaders: function() {
        var headers = this.rootTag.find('.block.header');
        for(var i = 0; i < headers.length; i++) {
            var header = $(headers[i]);
            var content = header.find('view-element').html();
            header.html('').addClass('html-content');
            header.append(
                '<div class="col-sm-1">&nbsp;</div>' +
                '<div class="col-sm-1">&nbsp;</div>' +
                '<div class="col-sm-10 block-content">' +
                    content +
                '</div>'
            );
        }
    }

    ,reDrawModuleData: function() {
        this.rootTag.find('module-title, module-background-image, module-questions, module-description').remove();
        var title = this.moduleTitle.split(' ');
        this.rootTag.find('module').addClass('module').prepend(
            '<div class="row">' +
                '<div class="col-sm-4 title">' +
                    '<div class="wrap">' +
                        '<span>' + title[0] + '</span> <br />' +
                        '<strong>'+ title[1] + '</strong>' +
                    '</div>' +
                '</div>' +
                '<div class="col-sm-8 wallpaper">' +
                    '<div class="wrap"></div>' +
                '</div>' +
            '</div>' +
            '<div class="row">' +
                '<div class="col-sm-4 questions">' + this.moduleQuestions + '</div>' +
                '<div class="col-sm-8 description">' +
                    this.moduleDescription +
                '</div>' +
            '</div>'
        );
    }
}
