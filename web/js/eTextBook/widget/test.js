var eTextBookWidgetTest = eTextBookWidget.extend({
    defaults: {
        slug: "test",
        title: "Тест",
        templateName: 'testSolutionWidget', ico: '<span class="glyphicon glyphicon-ok"></span>'
    },

    startEdit: function () {
        var items = this.cont.find('.task');
        for (var i = 0; i < items.length; i++) {
            var item = $(items[i]);
            item.find('.question input').val(item.find('view-element h5').text());
            item.find('.choices-list').html(item.find('view-element .choices-list').html());
        }
    },

    finishEdit: function () {
        var items = this.cont.find('.task');
        for (var i = 0; i < items.length; i++) {
            var item = $(items[i]);
            item.find('view-element')
                .attr('data-right-answer-ID', item.find('.choices-list input:checked').attr('id'))
                .append(item.find('.choices-list').clone());
            item.find('view-element h5').html(item.find('.question input').val())
        }
    },

    activate: function () {
        this.cont = this.editCont.find('.test-widget');
        this.addTask();
        this.binds();
    },

    addEditElements: function () {
        var $tasks = this.cont.find('.task');
        for (var i = 0; i < $tasks.length; i++) {
            var $task = $($tasks[i]);
            if (!$task.find('edit-element').length)
                $task.append(App.eTextBookTemplate.getTemplate('testWidgetEditElement'));
        }
    },

    addTask: function () {
        this.addEditElements();
        var input = this.cont.find('.question input').last(),
            inputIsNotEmpty = input.val() !== '';
        if (inputIsNotEmpty) {
            this.cont.find('.question .glyphicon').last().addClass('glyphicon-remove')
                .removeClass('add glyphicon-plus');
            this.cont.append(App.eTextBookTemplate.getTemplate('testWidgetTask'));
            this.addEditElements();
        }
        else {
            input.focus();
        }
    },

    parseChoice: function (obj) {
        var $choices = obj.closest('.choices'),
            $input = $choices.find('.add-choice input'),
            value = $input.val(),
            inputIsNotEmpty = value !== '';

        if (inputIsNotEmpty) {
            var uid = App.eTextBookUtils.generateUID();
            $choices.find('.choices-list').append(App.eTextBookTemplate.getTemplate('choiceRadioButton'));
            $choices.find('.choiceRadioButton:last').attr('id', uid).attr('class', uid);
            $choices.find('label:last').attr('for', uid).html(value);
            $input.val('');
        }
        else {
            $input.focus();
        }

    },

    binds: function () {
        var $this = this;
        const ENTER_KEY = 13;

        $('.test-widget')
            .on('click', '.question .add', function () {
                $this.addTask();
            })
            .on('click', '.add-choice .add', function () {
                $this.parseChoice($(this));
            })
            .on('keyup', '.question input', function (e) {
                if (e.which === ENTER_KEY) {
                    $this.addTask();
                }
            })
            .on('keyup', '.add-choice input', function (e) {
                if (e.which === ENTER_KEY) $this.parseChoice($(this));
            })
            .on('click', '.glyphicon-remove', function () {
                $(this).closest('.delete-point').remove();
            })

            .on('change', '.choice input', function () {
                $('.' + $(this).attr('class')).prop('checked', true);
            });
    },

    viewActivate: function () {
        this.contentContainer.find('.choice input').bind('change', function () {
            var task = $(this).closest('view-element');
            var selectedID = $(this).attr('id');
            console.log('right here');
            var rightID = task.attr('data-right-answer-id');
            console.log(rightID + ' and selected ' + selectedID);
            if (rightID === '') {
                task.removeClass('failed');
                task.removeClass('success');
            } else {
                if (selectedID !== rightID) {
                    task.addClass('failed');
                    task.removeClass('success');
                } else {
                    task.removeClass('failed');
                    task.addClass('success');
                }
            }
        });
    }


});

App.eTextBookWidgetRepository.registerWidget(eTextBookWidgetTest);