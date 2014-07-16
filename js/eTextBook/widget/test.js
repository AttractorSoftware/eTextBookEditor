var eTextBookWidgetTest = eTextBookWidget.extend({
    defaults: {
        slug: "test",
        title: "Тест",
        templateName: 'testSolutionWidget'
    }, activate: function () {
        this.addQuestion(this.editCont.find('.test-widget'));
        this.binds();
    }, addQuestion: function (obj) {
        obj.append(
            '<div class="task">' +
                '<edit-element class="add-question">' +
                '<div class="question"><label>Вопрос: </label>' +
                '<input type="text" placeholder="Вопрос">' +
                '<div class="add glyphicon glyphicon-plus"></div>' +
                '</div>' +
                '<div class="choices"><label>Варианты ответов: </label></div>' +
                '</edit-element>' +
                '</div>'
        );
        this.addChoice(obj.find('.add-question .choices:last'));
    }, addChoice: function (obj) {
        obj.append(
            '<edit-element class="add-choice">' +
                '<input type="text" placeholder="Вариант ответа">' +
                '<div class="add glyphicon glyphicon-plus"></div>' +
                '</edit-element>'
        );
    }, binds: function () {
        var $this = this;
        const ENTER_KEY = 13;


        $('.test-widget')
            .on('click', '.question .add', function () {
                addElement.call(this);
            })
            .on('click', '.add-choice .add', function () {
                addElement.call(this);
            })
            .on('keyup', '.question input', function (e) {
                if (e.which == ENTER_KEY) addElement.call(this);
            })
            .on('keyup', '.add-choice input', function (e) {
                if (e.which == ENTER_KEY) addElement.call(this);
            })
            .on('click', '.glyphicon-remove', function () {
                $(this).closest('edit-element').remove();
            });

        function addElement() {
            var closestEditElement = $(this).closest('edit-element');
            var closestGlyphIcon = closestEditElement.find('.glyphicon').first();

            if (closestGlyphIcon.hasClass("add")) {
                if (closestEditElement.hasClass("add-question"))
                    $this.addQuestion($(this).closest(".test-widget"));
                else if (closestEditElement.hasClass("add-choice"))
                    $this.addChoice($(this).closest(".choices"));
                closestGlyphIcon.addClass('glyphicon-remove').removeClass('add glyphicon-plus');
            }
        }

    }

});

App.eTextBookWidgetRepository.registerWidget(eTextBookWidgetTest);