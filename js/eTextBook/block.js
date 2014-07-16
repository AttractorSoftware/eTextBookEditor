var eTextBookBlock = Backbone.Model.extend({
    initialize: function () {
        this.cont = this.get('cont');
        this.editElements = {};
        this.editable = false;
        this.addControlPanel();
        this.addEditElements();
        this.activateWidget();
        this.activateWidgetSelect();
    }, addControlPanel: function () {

        var $this = this;

        this.get('cont')
            .prepend(App.eTextBookTemplate.getTemplate('blockControlPanel'))
            .append(this.get('module').generateAddBlockButton())
            .find('control-panel .edit').bind('click', function () {
                if (!$this.editable)
                    $this.startEdit();
                else
                    $this.finishEdit();
            });

        this.get('cont').find('control-panel .remove').bind('click', function () {
            $this.get('cont').remove();
            $this = null;
            App.eTextBookEditor.updateDisplay();
        });
    }, startEdit: function () {
        this.get('cont').attr('editable', 1);
        this.editable = true;
//        jQuery.each(this.editElements, this.startEdit());
        for (var i in this.editElements) {
            this.editElements[i].startEdit();
        }
    }, finishEdit: function () {
        this.get('cont').attr('editable', 0);
        this.editable = false;
        for (var i = 0 in this.editElements) {
            this.editElements[i].finishEdit();
        }
        App.eTextBookEditor.updateDisplay();
    }, addEditElements: function () {
        this.appendEditElement('title', new inlineEditInput({
            cont: this.cont.find('block-title')
        }));
    }, appendEditElement: function (title, element) {
        this.editElements[title] = element;
    }, activateWidget: function () {
        var widgetSlug = this.cont.find('widget').attr('widget-slug');
        if (widgetSlug) {
            var widgetConstruct = App.eTextBookWidgetRepository.getWidgetBySlug(widgetSlug);
            this.widget = new widgetConstruct();
            this.widget.editCont = this.cont.find('widget-content');
            this.widget.activate();
            this.appendEditElement('widget', this.widget);
        }
    }, activateWidgetSelect: function () {

        var $this = this;
        var selector = $('<select class="widget-selector"></select>');
        var label = $('<label>Тип задания: </label>');
        this.cont.find('widget').prepend(label, selector);
        selector.empty();

        var widgetSlug = this.cont.find('widget').attr('widget-slug');

        for (var i = 0; i < App.eTextBookWidgetRepository.widgets.length; i++) {
            var widgetConstruct = App.eTextBookWidgetRepository.widgets[i];
            var widget = new widgetConstruct();
            var selected = widgetSlug == widget.get('slug') ? 'selected' : '';
            selector.append('<option value="'
                + widget.get('slug') + '" ' + selected + '>'
                + widget.get('title') + '</option>');
        }

        selector.bind('change', function () {
            var widgetConstruct = App.eTextBookWidgetRepository.getWidgetBySlug($(this).val());
            var widget = new widgetConstruct();
            var template = $(App.eTextBookTemplate.getTemplate(widget.get('templateName')));
            $this.cont.find('widget-content').empty();
            $this.cont.find('widget-content').append(template);
            widget.editCont = $this.cont.find('widget-content');
            widget.activate();
            $this.appendEditElement('widget', widget);
            widget.startEdit();
            $this.cont.find('widget').attr('widget-slug', widget.get('slug'));
        });
    }

});