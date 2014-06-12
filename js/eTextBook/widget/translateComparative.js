var eTextBookWidgetTranslateComparative = eTextBookWidget.extend({
    defaults: {
        slug: "translate-comparative"
        ,title: "Сравнение перевода"
        ,templateName: 'translateComparativeWidget'
    }

    ,finishEdit: function() {

    }

    ,startEdit: function() {

    }

    ,activate: function() {

        var $this = this;

        this.editCont.find('translate-comparative').append('<edit-element class="new-item"><input class="word" /><input class="translate" /> <add class="glyphicon glyphicon-plus"></add></edit-element>');
        this.editCont.find('translate-comparative list item').append('<edit-element class="remove glyphicon glyphicon-remove"></edit-element>');
        this.editCont.find('translate-comparative list item .remove').bind('click', function() {
            $this.editCont.find('translate-comparative answers item[answer-id=' + $(this).parent().attr('answer-id') + ']').remove();
            $(this).parent().remove();
        });

        this.editCont.find('translate-comparative .new-item add').bind('click', function() {
            var word = $(this).parent().find('.word').val();
            var answer = $(this).parent().find('.translate').val();
            var id = Math.ceil(Math.random() * 10000);
            $this.editCont.find('translate-comparative list').append('<item item-id="' + id + '" answer-id="'+ id +'">'+ word +'</item>');
            $this.editCont.find('translate-comparative answers').append('<item answer-id="'+ id +'">'+ answer +'</item>');
            $(this).parent().find('.word').val("");
            $(this).parent().find('.translate').val("");
        });
    }
});

App.eTextBookWidgetRepository.registerWidget(eTextBookWidgetTranslateComparative);