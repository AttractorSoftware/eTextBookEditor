var App = function() {

    var $this = this;

    this.screens = {};

    this.screens.loading = new Screen($('#loading.screen'));
    this.screens.shelf = new Screen($('#shelf.screen'));

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
            $this.drawShelfs();
            $this.hideAllScreens();
            $this.screens.shelf.show();
        }, 3000);
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
            shelf.find('.book-list').append(this.createBook(this.storageBooks[i]));
        }
        $('.storageList .book').click(function() {
            Android.readBook($(this).attr('href'));
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
            shelf.find('.book-list').append(this.createBook(this.remoteBooks[i]));
        }
    }

    this.createBook = function(book) {
        return $('<a href="'+ book.slug +'" class="book" style="background-image: url(file:///sdcard/eTextBook/cache/'+ book.slug +'/content/cover.png)"></a>');
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
    }

    this.init();
}

$(function(){ app = new App(); });