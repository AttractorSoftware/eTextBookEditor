var clearPrint = {
    setRootElement: function(root) {
        this.rootElement = root;
    }

    ,clearFormElements: function() {
        this.rootElement.find('select, input').remove();
    }
}

$(function(){
   clearPrint.setRootElement($('e-text-book'));
   clearPrint.clearFormElements();
});