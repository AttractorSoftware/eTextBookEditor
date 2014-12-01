var eTextBookTemplate = Backbone.Model.extend({
    initialize: function() {
        this.templates = [];
    }

    ,getTemplate: function(templateName) {
        return $('.templates .template[name=' + templateName + ']').html();
    }

    ,getTemplateWithParams: function(templateName) {
        return _.template($('.templates .template[name=' + templateName + ']').html());
    }
});

App.eTextBookTemplate = new eTextBookTemplate();