var App = function() {

    var $this = this;

    this.screens = {};

    this.screens.loading = new Screen($('#loading.screen'));
    this.screens.shelf = new Screen($('#shelf.screen'));
    this.screens.uploading = new Screen($('#uploading.screen'));

    this.storageBooks = [];
    this.remoteBooks = [];

    this.init = function() {

        var is_resize = false;

        $(window).resize(function() {
            if(!is_resize) {
                is_resize = true;
                this.setTimeout(function() {
                    for(var i in $this.screens) {
                        $this.screens[i].calculateSizes();
                    }
                    $this.drawShelfs();
                    is_resize = false;
                }, 500);
            }
        });

        setTimeout(function(){
            Android.getStorageBookList();
            Android.getRepositoryBookList();
            $this.switchScreen('shelf');
        }, 2000);
    }

    this.switchScreen = function(screen) {
        this.hideAllScreens();
        this.screens[screen].show();
    }

    this.drawShelfs = function() {
        $this.renderStorageBooks();
        $this.renderRemoteBooks();
    }

    this.renderStorageBooks = function() {
        $('.storageList').html('');
        var shelf = this.createShelf();
        $('.storageList').append(shelf);
        for(var i = 0, j = 0; i < this.storageBooks.length; i++, j++) {
            if(j == Math.round((this.screens.shelf.width - 126) / 65)) {
                shelf = this.createShelf();
                $('.storageList').append(shelf);
                j = 0;
            }
            shelf.find('.book-list').append(this.createStorageBook(this.storageBooks[i]));
        }
        $('.storageList .book').click(function() {
            if($(this).hasClass('source')) {
                Android.readSource($(this).attr('href'), $(this).attr('source'));
            } else { Android.readBook($(this).attr('href')); }

            return false;
        });
    }

    this.renderRemoteBooks = function() {
        $('.remoteList').html('');
        var shelf = this.createShelf();
        $('.remoteList').append(shelf);
        for(var i = 0, j = 0; i < this.remoteBooks.length; i++, j++) {
            if(j == Math.round((this.screens.shelf.width - 126) / 65)) {
                shelf = this.createShelf();
                $('.remoteList').append(shelf);
                j = 0;
            }
            shelf.find('.book-list').append(this.createRemoteBook(this.remoteBooks[i]));
        }
        $('.remoteList .book').click(function() {
            var book = $(this);
            $this.switchScreen('uploading');
            $('#uploading.screen').css({ backgroundImage: $(book).css('backgroundImage')});
            $('#uploading.screen .book-title').html(book.attr('title'));
            setTimeout(function(){
                Android.downloadBook(book.attr('href'));
            }, 500)
            return false;
        });
    }

    this.createStorageBook = function(book) {
        if(book.source != '') {
            return $('<a href="'+ book.slug +'" title="' + book.title + '" class="book source" source="'+ book.source +'" style="background-image: url(file:///sdcard/eTextBook/cache/'+ book.slug +'/content/cover.png)"></a>');
        } else {
            return $('<a href="'+ book.slug +'" title="' + book.title + '" class="book" style="background-image: url(file:///sdcard/eTextBook/cache/'+ book.slug +'/content/cover.png)"></a>');
        }
    }

    this.createRemoteBook = function(book) {
        return $('<a href="'+ book.slug +'" title="' + book.title + '" class="book" style="background-image: url(http://textbooks-demo.it-attractor.net/publicBooks/'+ book.slug +'/content/cover.png)"></a>');
    }

    this.createShelf= function() {
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

    this.updateUploadingProgress = function(percents) {
        $('.uploading-progress .fill').css({ width: percents + '%'});
        $('.uploading-progress .display').html(percents + '%');
    }

    this.setRemoteBooks = function(books) {
        this.remoteBooks = books;
    }

    this.setStorageBooks = function(books) {
        this.storageBooks = books;
    }

    this.hideAllScreens = function() {
        for(var i in this.screens) {
            this.screens[i].hide();
        }
    };

    this.init();
};

$(function(){ app = new App(); });