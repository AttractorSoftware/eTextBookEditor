var eTextBookBuilder = function() {

    var $this = this;
    this.titleInput = $('#book-title');
    this.submitButton = $('.btn.save');
    this.bookList = $('#book-list');

    this.init = function() {
        this.submitButton.bind('click', function() {
            $this.save();
        });
        this.bookList.bind('change', function(){
            if($(this).val() == '') {
                location.href = "/";
            } else {
                location.href = "/?book=" + $(this).val();
            }
        });
    };

    this.save = function() {
        $.post('/pack.php', {
            title: this.titleInput.val(),
            content: App.eTextBookEditor.getContent()
        }, function(response) {
            location.href = "/?book=" + response;
        });
    };

    this.init();
};

App.eTextBookBuilder = new eTextBookBuilder();