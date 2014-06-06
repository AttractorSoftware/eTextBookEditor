var inlineEdit = Backbone.Model.extend({

    defaults : {
        editElementClass: ''
    }

    ,initialize: function() {
        this.cont = this.get('cont');
        this.viewCont = this.cont.find('view-element');
        this.addWidget();
    }

    ,addWidget: function() {
        alert("inlineEditInterface: addWidget method must be defined");
    }

    ,startEdit: function() {
        alert("inlineEditInterface: startEdit method must be defined");
    }

    ,finishEdit: function() {
        alert("inlineEditInterface: finishEdit method must be defined");
    }

});