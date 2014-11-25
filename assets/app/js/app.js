var App = function () {

    var $this = this;

    this.screens = {};

    this.screens.loading = new Screen($('#loading.screen'));
    this.screens.shelf = new Screen($('#shelf.screen'));
    this.screens.uploading = new Screen($('#uploading.screen'));

    this.storageBooks = [];
    this.remoteBooks = [];

    this.init = function () {

        var is_resize = false;

        $(window).resize(function () {
            if (!is_resize) {
                is_resize = true;
                this.setTimeout(function () {
                    for (var i in $this.screens) {
                        $this.screens[i].calculateSizes();
                    }
                    $this.drawShelfs();
                    is_resize = false;
                }, 500);
            }
        });

        this.repositoryUrl = Android.getRepositoryUrl();

        setTimeout(function(){
            Android.getStorageBookList();
            Android.getRepositoryBookList();
            $this.switchScreen('shelf');
        }, 2000);
    }

    this.markUpdateBook = function() {
        for(var i = 0; i < this.storageBooks.length; i++) {
            var book = this.storageBooks[i];
            var updateVersionSlug = this.getUpdateVersionSlug(book);
            if(updateVersionSlug) {
                $('.storageList #' + book.slug).addClass('has-update').attr('update-slug', updateVersionSlug);
            }
        }
    }

    this.bookSlugSplit = function(slug) {
        var slugParts = slug.split('-');
        return {
            id: slugParts[0],
            version: slugParts[slugParts.length-1]
        }
    }

    this.getUpdateVersionSlug = function(book) {
        var bookData = this.bookSlugSplit(book.slug);
        for(var i = 0; i < this.remoteBooks.length; i++) {
            var remoteBook = this.remoteBooks[i];
            var remoteBookData = this.bookSlugSplit(remoteBook.slug);
            if(bookData.id == remoteBookData.id && remoteBookData.version > bookData.version) {
                return remoteBook.slug;
            }
        } return false;
    }

    this.hideAlreadyDownloadBooks = function() {
        for(var i = 0; i < this.storageBooks.length; i++) {
            var storageBook = this.storageBooks[i];
            var storageBookData = this.bookSlugSplit(storageBook.slug);
            for(var j = 0; j < this.remoteBooks.length; j++) {
                var remoteBook = this.remoteBooks[j];
                var remoteBookData = this.bookSlugSplit(remoteBook.slug);
                if(remoteBookData.id == storageBookData.id) {
                    if($($('.remoteList .book')[j]).attr('id') == remoteBook.slug) {
                        $($('.remoteList .book')[j]).hide();
                    }
                }
            }
        }
    }

    this.switchScreen = function(screen) {
        this.hideAllScreens();
        this.screens[screen].show();
    }

    this.drawShelfs = function () {
        $this.renderStorageBooks();
        $this.renderRemoteBooks();
        $this.markUpdateBook();
        $this.hideAlreadyDownloadBooks();
    }

    this.renderStorageBooks = function () {
        $('.storageList').html('');
        var shelf = this.createShelf();
        $('.storageList').append(shelf);
        for (var i = 0, j = 0; i < this.storageBooks.length; i++, j++) {
            if (j == Math.round((this.screens.shelf.width - 126) / 65)) {
                shelf = this.createShelf();
                $('.storageList').append(shelf);
                j = 0;
            }
            shelf.find('.book-list').append(this.createStorageBook(this.storageBooks[i]));
        }

        $('.storageList .book').click(function() {
            if($(this).hasClass('has-update')) {
                if(!confirm('Доступна новая версия книги. Загрузить обновленную версию?')) {
                    if($(this).hasClass('source')) {
                        Android.readSource($(this).attr('href'), $(this).attr('source'));
                    } else { Android.readBook($(this).attr('href')); }
                } else {
                    var book = $(this);
                    $this.switchScreen('uploading');
                    $('#uploading.screen').css({ backgroundImage: $(book).css('backgroundImage')});
                    $('#uploading.screen .book-title').html(book.attr('title'));
                    setTimeout(function(){
                        Android.downloadBook(book.attr('update-slug'));
                    }, 500);
                    $this.downloadComplete = function() {
                        if(Android.removeOldBook($(book).attr('href'))) {
                            $('.storageList #' + $(book).attr('href')).remove();
                        }
                    };
                }
            } else {
                if($(this).hasClass('source')) {
                    Android.readSource($(this).attr('href'), $(this).attr('source'));
                } else { Android.readBook($(this).attr('href')); }
            }

            return false;
        });
    }

    this.renderRemoteBooks = function () {
        $('.remoteList').html('');
        var shelf = this.createShelf();
        $('.remoteList').append(shelf);
        for (var i = 0, j = 0; i < this.remoteBooks.length; i++, j++) {
            if (j == Math.round((this.screens.shelf.width - 126) / 65)) {
                shelf = this.createShelf();
                $('.remoteList').append(shelf);
                j = 0;
            }
            shelf.find('.book-list').append(this.createRemoteBook(this.remoteBooks[i]));
        }
        $('.remoteList .book').click(function () {
            var book = $(this);
            $this.switchScreen('uploading');
            $('#uploading.screen').css({ backgroundImage: $(book).css('backgroundImage')});
            $('#uploading.screen .book-title').html(book.attr('title'));
            setTimeout(function () {
                Android.downloadBook(book.attr('href'));
            }, 500);
            $this.downloadComplete = function(){};
            return false;
        });
    }

    this.createStorageBook = function(book) {
        if(book.source != '' && book.source != 'null') {
            return $('<a href="'+ book.slug +'" id="'+ book.slug +'" title="' + book.title + '" class="book source" source="'+ book.source +'" style="background-image: url(file:///sdcard/eTextBook/cache/'+ book.slug +'/content/cover.png)"><span class="update-ico"></span></a>');
        } else {
            return $('<a href="'+ book.slug +'" id="'+ book.slug +'" title="' + book.title + '" class="book" style="background-image: url(file:///sdcard/eTextBook/cache/'+ book.slug +'/content/cover.png)"><span class="update-ico"></span></a>');
        }
    }

    this.createRemoteBook = function(book) {
        return $('<a href="'+ book.slug +'" id="'+ book.slug +'" title="' + book.title + '" class="book" style="background-image: url(' + $this.repositoryUrl + '/publicBooks/'+ book.slug +'/content/cover.png)"></a>');
    }

    this.createShelf = function () {
        var id = Math.ceil(Math.random() * 100000);
        return $(
                '<div class="shelf" id="' + id + '">' +
                '<div class="left-side">' +
                '<div class="right-side">' +
                '<div class="middle-side">' +
                '<div class="book-list"></div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>'
        );
    }

    this.updateUploadingProgress = function (percents) {
        $('.uploading-progress .fill').css({ width: percents + '%'});
        $('.uploading-progress .display').html(percents + '%');
    }

    this.setRemoteBooks = function (books) {
        this.remoteBooks = books;
    }

    this.setStorageBooks = function (books) {
        this.storageBooks = books;
    }

    this.hideAllScreens = function () {
        for (var i in this.screens) {
            this.screens[i].hide();
        }
    };

    this.downloadComplete = function() {}

    this.init();
};

$(function () { app = new App(); });
