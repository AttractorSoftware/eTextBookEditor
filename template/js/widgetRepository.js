var eTextBookWidgetRepository = Backbone.Model.extend({

    initialize: function() {
        this.widgets = [];
    }

    ,registerWidget: function(widget) {
        this.widgets.push(widget);
    }

    ,getWidgetBySlug: function(slug) {
        for(var i in this.widgets) {
            var widget = new this.widgets[i]();
            if(widget.get('slug') == slug) {
                return this.widgets[i];
            }
        } return false;
    }

});

App.eTextBookWidgetRepository = new eTextBookWidgetRepository();