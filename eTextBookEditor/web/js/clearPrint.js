var clearPrint = {
    setRootElement: function(root) {
        this.rootElement = root;
    }

    ,clearFormElements: function() {
        this.rootElement.find('select, input').remove();
        this.rootElement.find('image-description image-title').show();
        this.rootElement.find('image-description image-view span').remove();
        this.rootElement.find('image-description descs').remove();
    }
}

$(function(){
   clearPrint.setRootElement($('e-text-book'));
   clearPrint.clearFormElements();
});