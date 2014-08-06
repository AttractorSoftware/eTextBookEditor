var eTextBookWidgetTranslateComparative = eTextBookWidget.extend({
    defaults: {
        slug: "translate-comparative"
        ,title: "Сравнение перевода"
        ,templateName: 'translateComparativeWidget'
        ,ico: '<span class="glyphicon glyphicon-random"></span>'
    }

    ,finishEdit: function() {

    }

    ,startEdit: function() {

    }

    ,bindRemoveEvent: function() {
        var $this = this;
        this.editCont.find('list item .remove').unbind('click');
        this.editCont.find('list item .remove').bind('click', function() {
            $this.editCont.find('translate-comparative answers item[answer-id=' + $(this).parent().attr('answer-id') + ']').remove();
            $(this).parent().remove();
        });
    }

    ,activate: function() {

        this.sortAnswers();

        var $this = this;

        this.editCont.find('translate-comparative').append('<edit-element class="new-item"><input class="word" placeholder="Слово" /><input class="translate" placeholder="Перевод" /> <add class="glyphicon glyphicon-plus"></add></edit-element>');
        this.editCont.find('list item').append('<edit-element class="remove glyphicon glyphicon-remove"></edit-element>');
        this.bindRemoveEvent();

        this.editCont.find('translate-comparative .new-item add').bind('click', function() {
            var word = $(this).parent().find('.word').val();
            var answer = $(this).parent().find('.translate').val();
            var id = Math.ceil(Math.random() * 10000);
            $this.editCont.find('translate-comparative list').append('<item item-id="' + id + '" answer-id="'+ id +'">'+ word +'<edit-element class="remove glyphicon glyphicon-remove"></edit-element></item>');
            $this.editCont.find('translate-comparative answers').append('<item answer-id="'+ id +'">'+ answer +'</item>');
            $(this).parent().find('.word').val("");
            $(this).parent().find('.translate').val("");
            $this.bindRemoveEvent();
        });
    }

    ,viewActivate: function() {

        this.randomizeAnswers();

        var $this = this;

        var listItems = this.contentContainer.find('list item');
        var answersItems = this.contentContainer.find('answers item');

        listItems.removeClass('selected failed success');
        answersItems.removeClass('selected failed success');

        listItems.bind('click', function() {
            if(!$(this).hasClass('success')) {
                listItems.removeClass('selected failed');
                answersItems.removeClass('selected failed');
                $(this).addClass('selected');
            }
        });

        answersItems.bind('click', function() {
            var selected = $this.contentContainer.find('list item.selected');
            if(selected.length) {
                if(selected.attr('answer-id') == $(this).attr('answer-id')) {
                    selected.addClass('success').removeClass('selected');
                    $(this).addClass('success');
                } else {
                    selected.addClass('failed').removeClass('selected');
                    $(this).addClass('failed');
                }
            }
        });
    }

    ,sortAnswers: function() {
        var answers = this.editCont.find('answers item');
        this.editCont.find('answers').html('');
        for(var i = 0; i < this.editCont.find('list item').length; i++) {
            var listItem = $(this.editCont.find('list item')[i]);
            for(var j = 0; j < answers.length; j++) {
                if(listItem.attr('answer-id') == $(answers[j]).attr('answer-id')) {
                    this.editCont.find('answers').append(answers[j]);
                }
            }
        }
    }

    ,randomizeAnswers: function() {
        var answers = this.contentContainer.find('answers item');
        this.contentContainer.find('answers').html('')
            .append(
                answers.sort(function() {
                    return Math.random() > 0.5 ? 1 : -1;
                })
            );
    }
});

App.eTextBookWidgetRepository.registerWidget(eTextBookWidgetTranslateComparative);