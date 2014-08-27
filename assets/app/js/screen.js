var Screen = function(cont) {
    var $this = this;
    this.cont = cont;

    this.init = function() {
        this.calculateSizes();
    }

    this.calculateSizes = function() {
        this.width = $('body').width();
        this.height= $('body').height();
    }

    this.hide = function() {
        this.cont.hide();
    }

    this.show = function() {
        this.cont.show();
    }

    this.init();
}