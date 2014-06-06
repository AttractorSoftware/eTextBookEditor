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
    }

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
    }
}

App.eTextBookUtils = new eTextBookUtils();