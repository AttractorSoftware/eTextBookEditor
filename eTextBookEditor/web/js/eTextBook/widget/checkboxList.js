var eTextBookWidgetCheckboxList = eTextBookWidget.extend({
    defaults: {
        slug: "checkbox-list"
        ,title: "Вхождение в множество"
        ,templateName: 'checkboxListWidget'
        ,ico: '<span class="glyphicon glyphicon-log-in"></span>'
    }

    ,finishEdit: function() {

    }

    ,startEdit: function() {

    }

    ,activate: function() {
        this.cont = this.editCont.find('.checkbox-list');
        this.appendAddElement();
        this.markChecked();
        this.bindItems();
    }

    ,markChecked: function() {
        for(var i = 0; i < this.cont.find('.checkboxes .item').length; i++) {
            var checkbox = $(this.cont.find('.checkboxes .item')[i]);
            if(checkbox.hasClass('checked')) {
                checkbox.find('input').prop('checked', true);
            } else {
                checkbox.find('input').prop('checked', false);
            }
        }
    }

    ,appendAddElement: function() {
        var $this = this;
        this.cont.prepend(App.eTextBookTemplate.getTemplate('checkboxListWidgetAddElement'));
        this.addElement = this.cont.find('.add-checkbox');
        this.addElement.find('input').bind('keyup', function(e) {
            if(e.which == 13) {
                $this.addItem();
            }
        });
        this.addElement.find('.add').bind('click', function(){
           $this.addItem();
        });
    }

    ,addItem: function() {
        var word = this.addElement.find('input').val();
        if(word.length) {
            var item = $('<div class="item"><input type="checkbox" /><span class="title">'+ word +'</span></div>');
            this.cont.find('.checkboxes').append(item);
            this.addElement.find('input').val('');
            this.bindItems();
        }
    }

    ,bindItems: function() {
        this.cont.find('.checkboxes .item input').unbind('click');
        this.cont.find('.checkboxes .item input').bind('click', function(){
            if($(this).prop('checked')) {
                $(this).parent().addClass('checked');
            } else { $(this).parent().removeClass('checked'); }
        });
    }

    ,viewActivate: function() {
        this.contentContainer.find('.checkboxes .item input').prop('checked', false);
        this.contentContainer.find('.checkboxes .item input').bind('click', function(){
           var must = $(this).parent().hasClass('checked');
           if($(this).prop('checked') && must){
               $(this).parent().addClass('success');
               App.animate($(this).parent(), 'pulse');
           } else {
               if($(this).prop('checked') && !must) {
                   $(this).parent().addClass('failed');
                   App.animate($(this).parent(), 'tada');
               } else {
                   $(this).parent().removeClass('success').removeClass('failed');
               }
           }
        });
    }
});

App.eTextBookWidgetRepository.registerWidget(eTextBookWidgetCheckboxList);