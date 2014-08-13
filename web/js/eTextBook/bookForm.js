var bookForm = function() {

    var $this = this;
    this.modal = $('#bookFormModal');
    this.alertBox = this.modal.find('#alertBox');
    this.action = $this.modal.find('form').attr('action');

    this.init = function() {
        this.cover = new AjaxUploader({
            fileInput: this.modal.find('#bookCover')
            ,afterLoad: function () {
                $this.modal.find('.book-cover .cover-view').css({
                    backgroundImage: 'url(' + $this.cover.dataUrlResult + ')'
                });}
            ,afterUpload: function() {
                var result = JSON.parse($this.cover.uploadResult);
                $this.coverFileName = result.fileName;
            }
        });

        this.modal.on('hidden.bs.modal', function (e) {
            $this.scope.$apply(function() {
                $this.scope.book = {};
            });
            $this.alertHide();
            $this.modal.find('.modal-footer .btn-primary').show();
            $this.modal.find('.cover-view').attr({ style: '' });
        });
    }

    this.failed = function(message) {
        this.alertBox.attr('class', 'alert alert-danger').html(message).show();
    }

    this.success = function(message) {
        this.alertBox.attr('class', 'alert alert-success').html(message).show();
    }

    this.alertHide = function() {
        this.alertBox.hide();
    }

    this.wait = function() {
        this.alertBox.attr('class', 'alert alert-info').html('Отправка данных... Пожалуйста подождите!').show();
        this.modal.find('.modal-footer .btn-primary').hide();
    }

    this.controller = function($scope) {

        $this.scope = $scope;

        $scope.submit = function(book) {
            $this.wait();
            book.cover = $this.coverFileName;
            $.post($this.action, { book: book }, function(response) {
                if(response.status == 'failed') {
                    $this.failed(response.reason);
                    $this.modal.find('.modal-footer .btn-primary').show();
                } else {
                    $this.success('Учебник успешно создан');
                    $('.book-list').append(
                        '<li id="' + response.data.slug + '" class="item">' +
                            '<div class="cover" style="background-image: url(/tmp/' + response.data.slug + '/content/cover.png)"></div>' +
                            '<a class="title" href="/book/view/' + response.data.slug + '/%20"> ' + book.title + '</a>' +
                            '<span class="authors"></span>' +
                            '<a class="edit-link btn btn-primary btn-xs" href="/book/edit/' + response.data.slug + '/%20">'+
                                'Редактировать'+
                            '</a>'+
                        '</li>'
                    );
                }
            });
        }

        $scope.reset = function() {
            $scope.book = {};
        }
    }

    if(this.modal.length) { this.init(); }
}


App.bookForm = new bookForm();


