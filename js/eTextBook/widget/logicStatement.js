var eTextBookWidgetLogicStatement = eTextBookWidget.extend({
    defaults: {
        slug: "logic-statement"
        ,title: "Логические утверждения"
        ,templateName: 'logicStatementWidget'
    }

    ,finishEdit: function() {
        for(var i = 0; i < this.cont.find('item').length; i++) {
            var item = $(this.cont.find('item')[i]);
            item.find('view-element').html(item.find('.text input').val());
            item.attr('value', item.find('.value select').val());
        }
    }

    ,startEdit: function() {
        for(var i = 0; i < this.cont.find('item').length; i++) {
            var item = $(this.cont.find('item')[i]);
            item.find('.text input').val(item.find('view-element').html());
            item.find('.value select').val(item.attr('value'));
        }
    }

    ,activate: function() {
        var $this = this;
        this.cont = this.editCont.find('logic-statement');
        this.cont.append(App.eTextBookTemplate.getTemplate('logicStatementAddInput'));
        this.appendEditElements();
        this.cont.find('.new-statement add').bind('click', function() {
            var text = $this.cont.find('.new-statement input').val();
                $this.cont.find('.new-statement input').val('');
            var value = $this.cont.find('.new-statement select').val();
                $this.cont.find('.new-statement select').val(0);
            var item = $(App.eTextBookTemplate.getTemplateWithParams('logicStatementItem')(
                { value: value, text: text }
            ));
            $this.cont.find('.new-statement').before(item);
            item.find('select').val(value);
            $this.bindRemoveEvent();
        });
    }

    ,appendEditElements: function() {
        for(var i = 0; i < this.cont.find('item').length; i++) {
            var item = $(this.cont.find('item')[i]);
            item.append(
                '<edit-element class="text">' +
                    '<input type="text" value=""></edit-element>' +
                '<edit-element class="value">' +
                    '<select>' +
                        '<option value="0">ката</option>' +
                        '<option value="1">тура</option>' +
                    '</select>' +
                '</edit-element>' +
                ' <edit-element class="remove glyphicon glyphicon-remove"></edit-element> '
            );
        } this.bindRemoveEvent();
    }

    ,bindRemoveEvent: function() {
        this.cont.find('item .remove').unbind('click');
        this.cont.find('item .remove').bind('click', function() {
            $(this).parent().remove();
        });
    }

    ,viewActivate: function() {
        this.contentContainer.find('item').append('<select class="not-saved"><option value=""></option><option value="0">ката</option><option value="1">тура</option></select>');
        this.contentContainer.find('item select').bind('change', function() {

            var selectedValue = $(this).val();
            var currentValue = $(this).parent().attr('value');

            if(selectedValue != '' && selectedValue != currentValue) {
                $(this).parent().addClass('failed');
                $(this).parent().removeClass('success');
            } else {
                if(selectedValue == '') {
                    $(this).parent().removeClass('failed');
                    $(this).parent().removeClass('success');
                } else {
                    $(this).parent().removeClass('failed');
                    $(this).parent().addClass('success');
                }
            }
        });
    }
});

App.eTextBookWidgetRepository.registerWidget(eTextBookWidgetLogicStatement);