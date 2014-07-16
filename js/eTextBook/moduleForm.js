var moduleForm = function() {

    var $this = this;
    this.modal = $('#moduleFormModal');
    this.alertBox = this.modal.find('#alertBox');
    this.action = '/create-module.php';

    this.init = function() {
        this.modal.on('hidden.bs.modal', function (e) {
            $this.scope.$apply(function() {
                $this.scope.module = {};
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

        $scope.submit = function(module) {
            $this.wait();
            module.bookSlug = $this.modal.find('form').attr('book-slug');
            $.post($this.action, { module: module }, function(response) {
                if(response.status == 'failed') {
                    $this.failed(response.reason);
                    $this.modal.find('.modal-footer .btn-primary').show();
                } else {
                    $this.success('Модуль успешно создан');
                    $('#addModuleBtn').parent().before('<li><a href="/editor.php?book=' + module.bookSlug + '&module=' + response.data.slug +'.html">' + module.title + '</a></li>');
                }
            });
        }

        $scope.reset = function() {
            $scope.book = {};
        }
    }

    this.init();
}


App.moduleForm = new moduleForm();


