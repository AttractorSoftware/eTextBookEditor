var bookForm = function() {

    var $this = this;
    this.modal = $('#bookFormModal');
    this.alertBox = this.modal.find('#alertBox');
    this.action = $this.modal.find('form').attr('action');

    this.init = function() {
        this.modal.on('hidden.bs.modal', function (e) {
            $this.scope.$apply(function() {
                $this.scope.book = {};
            });
            $this.alertHide();
            $this.modal.find('.modal-footer .btn-primary').show();
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
            $.post($this.action, { book: book }, function(response) {
                if(response.status == 'failed') {
                    $this.failed(response.reason);
                    $this.modal.find('.modal-footer .btn-primary').show();
                } else {
                    $this.success('Учебник успешно создан');
                    $('.book-list').append(
                        '<li>' +
                            '<span class="glyphicon glyphicon-book"></span>' +
                            '<a class="title" href="/book/view/' + response.data.slug + '/%20"> ' + book.title + '</a>' +
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

    this.init();
}


App.bookForm = new bookForm();


