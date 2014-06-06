var eTextBookTemplate = Backbone.Model.extend({
    initialize: function() {
        this.templates = [];
    }

    ,addTemplate: function(template) {
        this.templates.push(template);
    }

    ,getTemplate: function(templateName) {
        for(var i = 0; i < this.templates.length; i++) {
            if(this.templates[i].name == templateName) {
                return this.templates[i].content;
            }
        } return 'Template with name "' + templateName + '" not found';
    }
});

App.eTextBookTemplate = new eTextBookTemplate();