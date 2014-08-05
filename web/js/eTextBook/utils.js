var eTextBookUtils = function() {

    this.parseTextBlockFromHtml = function(text) {
        var strings = text.split("<br>");
        var result = "";
        for(var i = 0; i < strings.length; i++) {
            if(strings[i] != '') {
                var trimStr = $.trim(strings[i]);
                var newLine = i != strings.length-1 ? "\n" : '';
                result += trimStr + newLine;
            }
        }
        return result;
    };

    this.parseTextBlockToHtml = function(text) {
        var strings = text.split("\n");
        var result = "";
        for(var i = 0; i < strings.length; i++) {
            if(strings[i] != '') {
                var trimStr = $.trim(strings[i]);
                var newLine = i != strings.length-1 ? "<br>" : '';
                result += trimStr + newLine;
            }
        }
        return result;
    };

    this.generateUID = function() {
        var date = new Date();
        return  this.firstZero(date.getDate()) +
                this.firstZero(date.getMonth()) +
                this.firstZero(date.getFullYear()) +
                this.firstZero(date.getHours()) +
                this.firstZero(date.getMinutes()) +
                this.firstZero(date.getSeconds());
    };

    this.firstZero = function(value) {
        return value > 9 ? value : '0' + value;
    };

    this.markBackgroundImage = function(target) {
        var imageSrc = $(target).css('backgroundImage');
        imageSrc = imageSrc.replace('url(','').replace(')','');
        imageSrc = imageSrc.substr(1, imageSrc.length-2);
        var image = new Image();
        $(image).bind('load', function() {
            if($(this).prop('width') > $(this).prop('height')) {
                $(target).addClass('image-wide');
            } else {
                $(target).addClass('image-tall');
            }
        });
        image.src = imageSrc;
    }
};

App.eTextBookUtils = new eTextBookUtils();