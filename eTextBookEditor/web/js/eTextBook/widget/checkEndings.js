var eTextBookWidgetCheckEndings = eTextBookWidget.extend({
    defaults: {
        slug: "check-endings"
        ,title: Translator._("Проверка окончаний")
        ,templateName: 'checkEndingsWidget'
        ,ico: '<span class="glyphicon glyphicon-compressed"></span>'
    }

    ,finishEdit: function() {
        for(var i = 0; i < this.cont.find('.words .item').length; i++) {
            var item = $(this.cont.find('.words .item')[i]);
            item.find('view-element').html(item.find('input').val());
            item.attr('value', item.find('select').val());
        }
        var endingsList = '';
        for(var i = 0; i < this.cont.find('.endings .item').length; i++) {
            var item = $(this.cont.find('.endings .item')[i]);
            endingsList += item.find('input').val() + ', ';
        }
        this.cont.find('.endings-list').html(endingsList);
    }

    ,startEdit: function() {
        for(var i = 0; i < this.cont.find('.words .item').length; i++) {
            var item = $(this.cont.find('.words .item')[i]);
            item.find('input').val(item.find('view-element').html());
            item.find('select').val(item.attr('value'));
        }
    }

    ,activate: function() {
        var $this = this;
        this.cont = this.editCont.find('.check-endings');

        this.cont.append(
            '<edit-element class="endings">' +
                '<label class="title">Окончания:</label>' +
                '<div class="list">' +
                    '<edit-element class="add-ending">' +
                        '<input type="text">' +
                            '<div class="add glyphicon glyphicon-plus"></div>' +
                        '</edit-element>' +
                    '</div>' +
            '</edit-element>'
        );

        this.parseEndings();

        this.cont.find('.words .list').append(
            '<edit-element class="add-word">' +
                '<input type="text">' +
                '<div class="add glyphicon glyphicon-plus"></div>' +
            '</edit-element>'
        );

        this.cont.find('.words .item').append(
            '<edit-element>' +
                '<input type="text" value=""> ' +
                '<select></select>' +
            '</edit-element>' +
            '<edit-element class="glyphicon glyphicon-remove"></edit-element>'
        );

        this.updateWordSelects();
        this.bindEndingRemoveEvent();
        this.bindWordRemoveEvent();

        this.cont.find('.add-word .add').bind('click', function() { $this.addWord(); });
        this.cont.find('.add-word input').bind('keyup', function(e) {
            if(e.which == 13) { $this.addWord(); }
        });

        this.cont.find('.add-ending .add').bind('click', function() { $this.addEnding(); });
        this.cont.find('.add-ending input').bind('keyup', function(e) {
            if(e.which == 13) { $this.addEnding(); }
        });
    }

    ,addWord: function() {
        var value = this.cont.find('.add-word input').val();
        this.cont.find('.add-word').before(
            App.eTextBookTemplate.getTemplateWithParams('checkEndingsWordItem')({ value: value })
        );
        this.cont.find('.add-word input').val('');
        this.updateWordSelects();
        this.bindWordRemoveEvent();
    }

    ,addEnding: function() {
        var value = this.cont.find('.add-ending input').val();
        this.cont.find('.add-ending').before(
            App.eTextBookTemplate.getTemplateWithParams('checkEndingsEndingItem')({ value: value })
        );
        this.cont.find('.add-ending input').val('');
        this.updateWordSelects();
        this.bindEndingRemoveEvent();
    }

    ,parseEndings: function() {
        var values = this.cont.find('.endings-list').html().split(', ');
        for(var i = 0; i < values.length; i++) {
            if(values[i] != '') {
                this.cont.find('.endings .list .add-ending').before(
                    App.eTextBookTemplate.getTemplateWithParams('checkEndingsEndingItem')({ value: values[i] })
                );
            }
        }

    }

    ,bindWordRemoveEvent: function() {
        this.cont.find('.words .item .glyphicon-remove').unbind('click');
        this.cont.find('.words .item .glyphicon-remove').bind('click', function() {
            $(this).parent().remove();
        });
    }

    ,bindEndingRemoveEvent: function() {
        var $this = this;
        this.cont.find('.endings .item .glyphicon-remove').unbind('click');
        this.cont.find('.endings .item .glyphicon-remove').bind('click', function() {
            $(this).parent().remove();
            $this.updateWordSelects();
        });
    }

    ,updateWordSelects: function() {
        var result = '';
        for(var i = 0; i < this.cont.find('.endings .list .item').length; i++) {
            var item = $(this.cont.find('.endings .list .item')[i]);
            result += '<option value="' + item.find('input').val() + '">' + item.find('input').val() + '</option>';
        }

        for(var i = 0; i < this.cont.find('.words .list .item').length; i++) {
            var item = $(this.cont.find('.words .list .item')[i]);
            var select = item.find('select');
            var selectValue = select.val();
            select.html(result);
            select.val(selectValue);
        }
    }

    ,viewActivate: function() {
        var select = this.generateSelectFromEndings();
        this.contentContainer.find('.item').append(select);
        this.contentContainer.find('.item select').bind('change', function() {
            var selectedValue = $(this).val();
            var currentValue = $(this).parent().attr('value');
            if(selectedValue == '') {
                $(this).parent().removeClass('failed');
                $(this).parent().removeClass('success');
            } else {
                if(selectedValue != currentValue) {
                    $(this).parent().addClass('failed');
                    $(this).parent().removeClass('success');
                } else {
                    $(this).parent().removeClass('failed');
                    $(this).parent().addClass('success');
                }
            }
        });
    }

    ,generateSelectFromEndings: function() {
        var values = this.contentContainer.find('.endings-list').html().split(', ');
        var options = '<option></option>';
        for(var i = 0; i < values.length; i++) {
            if(values[i] != '') {
                options += '<option value="' + values[i] + '">' + values[i] + '</option>';
            }
        } return '<select class="not-saved">' + options + '</select>';
    }
});

App.eTextBookWidgetRepository.registerWidget(eTextBookWidgetCheckEndings);