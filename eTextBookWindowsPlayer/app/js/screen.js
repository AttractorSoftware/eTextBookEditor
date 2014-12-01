var Screen = function (cont) {
    this.body = $('body');
    this.cont = cont;

    this.init = function () {
        this.calculateSizes();
    };

    this.calculateSizes = function () {
        this.width = this.body.width() - 150;
        this.height = this.body.height();
    };

    this.hide = function () {
        this.cont.hide();
    };

    this.show = function () {
        this.cont.show();
    };

    this.init();
};