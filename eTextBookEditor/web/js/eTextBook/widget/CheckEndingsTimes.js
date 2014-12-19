var eTextBookWidgetCheckEndingsTimes = eTextBookWidget.extend({
    defaults: {
        slug: "check-endings-times"
        ,title: Translator._("Проверка окончаний времен")
        ,templateName: 'checkEndingsTimesWidget'
        ,ico: '<span class="glyphicon glyphicon-compressed"></span>'
    }

    ,finishEdit: function() {
        var items = this.cont.find('.words .list .item');
        for(var i = 0; i < items.length; i++) {
            var item = $(items[i]);
            item.find('view-element').html(item.find('input').val());
        }
    }

    ,startEdit: function() {
        var items = this.cont.find('.words .list .item');
        for(var i = 0; i < items.length; i++) {
            var item = $(items[i]);
            item.find('input').val(item.find('view-element').html());
        }
    }

    ,activate: function() {
        this.cont = this.editCont.find('.check-endings');
        this.renderWordEditElements();
        this.renderEndingsEditElements();
    }

    ,renderEndingsEditElements: function() {
        var $this = this;
        this.addEndingsElement = $(App.eTextBookTemplate.getTemplate('checkEndingsTimesEditElements'));
        this.cont.find('.words').after(this.addEndingsElement);
        // Bind events
        this.addEndingsElement.find('.future .add').bind('click', function(e) { $this.addEnding('future'); });
        this.addEndingsElement.find('.future input').bind('keyup', function(e) {
            if(e.which == 13) { $this.addEnding('future'); }
        });
        this.addEndingsElement.find('.real .add').bind('click', function(e) { $this.addEnding('real'); });
        this.addEndingsElement.find('.real input').bind('keyup', function(e) {
            if(e.which == 13) { $this.addEnding('real'); }
        });
        this.addEndingsElement.find('.past .add').bind('click', function(e) { $this.addEnding('past'); });
        this.addEndingsElement.find('.past input').bind('keyup', function(e) {
            if(e.which == 13) { $this.addEnding('past'); }
        });
        this.parseEndings();
    }

    ,parseEndings: function() {
        var $this = this;
        if(this.cont.find('.endings-list').html().length) {
            var endings = JSON.parse(this.cont.find('.endings-list').html());
            for(var key in endings) {
                for(var i = 0; i < endings[key].length; i++) {
                    $this.addEndingsElement.find('.' + key + ' .add-ending input').val(endings[key][i]);
                    $this.addEnding(key);
                }
            }
        }
    }

    ,addEnding: function(endingType) {
        var $this = this;
        var endingValue = this.addEndingsElement.find('.' + endingType + ' .add-ending input').val();
        if(endingValue.length) {
            var ending = $(App.eTextBookTemplate.getTemplateWithParams('checkEndingsTimesEndingItem')({ ending: endingValue }));
            this.addEndingsElement.find('.' + endingType + ' .list .add-ending').before(ending);
            ending.find('.glyphicon-remove').bind('click', function(){
                $(this).parent().remove();
                $this.collectEndings();
                $this.updateEndingSelects();
            });
            this.addEndingsElement.find('.' + endingType + ' .add-ending input').val('');
            this.collectEndings();
            this.updateEndingSelects();
        }
    }

    ,collectEndings: function() {
        var futureEndingsInputs = this.addEndingsElement.find('.future .list .item input');
        var realEndingsInputs = this.addEndingsElement.find('.real .list .item input');
        var pastEndingsInputs = this.addEndingsElement.find('.past .list .item input');
        var endingsList = { future: [], real: [], past: [] };
        for(var i = 0; i < futureEndingsInputs.length; i++) {
            endingsList.future.push($(futureEndingsInputs[i]).val());
        }
        for(var i = 0; i < realEndingsInputs.length; i++) {
            endingsList.real.push($(realEndingsInputs[i]).val());
        }
        for(var i = 0; i < pastEndingsInputs.length; i++) {
            endingsList.past.push($(pastEndingsInputs[i]).val());
        }
        this.cont.find('.endings-list').html(JSON.stringify(endingsList));
    }

    ,renderWordEditElements: function() {
        this.renderAddElement();
        for(var i = 0; i < this.cont.find('.words .list .item').length; i++) {
            var item = $(this.cont.find('.words .list .item')[i]);
            item.append(App.eTextBookTemplate.getTemplate('checkEndingsTimesWordItemEditElement'));
            item.find('.glyphicon-remove').bind('click', function() {
                $(this).parent().remove();
            });
        }
    }

    ,renderAddElement: function() {
        var $this = this;
        var addWordElement = $(
            '<edit-element class="add-word">' +
                '<input type="text">' +
                '<div class="add glyphicon glyphicon-plus"></div>' +
            '</edit-element>'
        );
        this.cont.find('.words .list').append(addWordElement);
        this.addWordInput = addWordElement.find('input');
        // Bind events
        this.addWordInput.bind('keyup', function(e) {
            if(e.which == 13) { $this.addWord(); }
        });
        addWordElement.find('.add').bind('click', function() { $this.addWord(); });
    }

    ,addWord: function() {
        var word = this.addWordInput.val();
        if(word.length) {
            var item = $(App.eTextBookTemplate.getTemplateWithParams('checkEndingsTimesWordItem')({
                word: word
            }));
            this.cont.find('.words .list .add-word').before(item);
            item.find('.glyphicon-remove').bind('click', function(){ $(this).parent().remove(); });
            item.find('select').bind('change', function() {
                if($(this).hasClass('futureList')) { $(this).parent().parent().attr({ future: $(this).val() });}
                if($(this).hasClass('realList')) { $(this).parent().parent().attr({ real: $(this).val() });}
                if($(this).hasClass('pastList')) { $(this).parent().parent().attr({ past: $(this).val() });}
            });
            this.addWordInput.val('');
            this.updateEndingSelects();
        }
    }

    ,generateEndingsOptions: function(endings) {
        var html = {
            future: '<option value="0"></option>'
            ,real: '<option value="0"></option>'
            ,past: '<option value="0"></option>'
        };
        for(var key in endings) {
            for(var i = 0; i < endings[key].length; i++) {
                html[key] += '<option value="' + endings[key][i] + '">' + endings[key][i] + '</option>';
            }
        } return html;
    }

    ,updateEndingSelects: function() {
        if(this.cont.find('.endings-list').html().length) {
            var html = this.generateEndingsOptions(JSON.parse(this.cont.find('.endings-list').html()));
        } else {
            var html = {
                future: ''
                ,real: ''
                ,past: ''
            }
        }

        this.cont.find('.words .item select.futureList').html(html.future);
        this.cont.find('.words .item select.realList').html(html.real);
        this.cont.find('.words .item select.pastList').html(html.past);
        for(var i = 0; i < this.cont.find('.words .item').length; i++) {
            var item = $(this.cont.find('.words .item')[i]);
            if(item.attr('future') != '') {
                item.find('select.futureList').val(item.attr('future'));
            }
            if(item.attr('past') != '') {
                item.find('select.pastList').val(item.attr('past'));
            }
            if(item.attr('real') != '') {
                item.find('select.realList').val(item.attr('real'));
            }
        }
    }

    ,viewActivate: function() {
        if(this.contentContainer.find('.endings-list').html() != '') {
            var html = this.generateEndingsOptions(JSON.parse(this.contentContainer.find('.endings-list').html()));
            var items = this.contentContainer.find('.words .item');
            this.contentContainer.find('.title').html(
                '<div class="e-title">өткөн <br /> чак</div>' +
                    '<div class="e-title">учур <br /> чак</div>' +
                    '<div class="e-title">келер <br /> чак</div>'
            );
            for(var i = 0; i < items.length; i++) {
                var item = $(items[i]);
                item.append('<select type="future" class="not-saved">' + html.future + '</select>');
                item.append('<select type="real" class="not-saved">' + html.real + '</select>');
                item.append('<select type="past" class="not-saved">' + html.past + '</select>');
            }
            items.find('select').bind('change', function() {
                var value = $(this).parent().attr($(this).attr('type'));
                if(value == $(this).val()) {
                    $(this).addClass('success');
                    $(this).removeClass('failed');
                } else {
                    $(this).addClass('failed');
                    $(this).removeClass('success');
                }
            });
        }
    }
});

App.eTextBookWidgetRepository.registerWidget(eTextBookWidgetCheckEndingsTimes);