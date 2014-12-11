var app = function() {
    var $this = this;
    this.translateList = [];
    this.animate = function(target, animation) {
        $(target).addClass(animation).addClass('animated');
        setTimeout(function(){
            $(target).removeClass(animation).removeClass('animated');
        }, 1000);
    };
};
App = new app();