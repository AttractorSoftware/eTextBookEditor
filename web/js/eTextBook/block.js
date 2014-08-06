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
        var label = $('<edit-element><label>Тип задания: </label></edit-element>');
        var widgetTypeList = $('<edit-element class="widget-type-list"></edit-element>');
        this.cont.find('widget').prepend(label, widgetTypeList);
        selector.empty();

        var widgetSlug = this.cont.find('widget').attr('widget-slug');

        for (var i = 0; i < App.eTextBookWidgetRepository.widgets.length; i++) {
            var widgetConstruct = App.eTextBookWidgetRepository.widgets[i];
            var widget = new widgetConstruct();
            var selected = widgetSlug == widget.get('slug') ? 'selected' : '';
            widgetTypeList.append('<div value="'
                + widget.get('slug') + '" class="item ' + selected + '">'
                + widget.get('ico')
                + widget.get('title') + '</div>');
        }

        widgetTypeList.find('.item').bind('click', function () {
            var widgetConstruct = App.eTextBookWidgetRepository.getWidgetBySlug($(this).attr('value'));
            $(this).parent().find('.item').removeClass('selected');
            $(this).addClass('selected');
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

        if($this.cont.find('widget-content').html() == '') {
            $($this.cont.find('.widget-type-list .item')[0]).trigger('click');
        }
    }

});