var eTextBookWidget = Backbone.Model.extend({
    defaults: {
        slug: 'widget-slug'
        ,title: 'Отображаемое название виджета'
    }

    ,render: function() {
        alert("widgetInterface: render method must be defined");
    }
});