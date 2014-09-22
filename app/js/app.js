var fs = require('fs'),
    path = require('path'),
    dirName = process.cwd() + '/app/',
    cachePath = "app:///ebook/app/books/cache/";


var App = function () {

        var $this = this;

        this.screens = {};

        this.screens.loading = new Screen($('#loading.screen'));
        this.screens.shelf = new Screen($('#shelf.screen'));

        this.storageList = $('.storageList');
        this.remoteList = $('.remoteList');

        this.openShelfPage = function () {
            try {
                window.location = "app://ebook/app.html";
                console.log('hey');
            } catch (e) {
                console.log('clicked, not relocated');
                return false;
            }
        };

        this.storageBooks = [];
        this.remoteBooks = [];

        this.getStorageBookList = function () {
            var filterFn = require('unzip.js');
            var bookList;

            filterFn(dirName, function (err, list) {
                if (!err) {
                    bookList = list;
                    app.setStorageBooks(bookList);
                    app.drawShelfs();
                }
                else console.log('error: ' + err)
            });
        };

        this.readBook = function (bookSlug) {
//        $('body').load(cachePath + bookSlug + "/index.html");
            window.location.assign(cachePath + bookSlug + "/index.html");
        };

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
            $this.getStorageBookList();

            $this.getStorageBookList();
            $this.drawShelfs();
            $this.hideAllScreens();
            $this.screens.shelf.show();
        };

        this.drawShelfs = function () {
            $this.renderStorageBooks();
            $this.renderRemoteBooks();
        };

        this.renderStorageBooks = function () {
            this.storageList.html('');
            var shelf = this.createShelf();
            this.storageList.append(shelf);
            for (var i = 0, j = 0; i < this.storageBooks.length; i++, j++) {
                if (j == Math.round((this.screens.shelf.width - 126) / 65)) {
                    shelf = this.createShelf();
                    $('.storageList').append(shelf);
                    j = 0;
                }
                shelf.find('.book-list').append(this.createBook(this.storageBooks[i]));
            }
            $('.storageList .book').click(function () {
                $this.readBook($(this).attr('href'));
                return false;
            });
        };

        this.renderRemoteBooks = function () {
            this.remoteList.html('');
            var shelf = this.createShelf();
            this.remoteList.append(shelf);
            for (var i = 0, j = 0; i < this.remoteBooks.length; i++, j++) {
                if (j == Math.round((this.screens.shelf.width - 126) / 65)) {
                    shelf = this.createShelf();
                    $('.remoteList').append(shelf);
                    j = 0;
                }
                shelf.find('.book-list').append(this.createBook(this.remoteBooks[i]));
            }
        };

        this.createBook = function (book) {
            book.slug = path.basename(book);
            return $('<a href="' + path.basename(book) +
                         '" class="book" style="background-image: url(' +
                         cachePath + path.basename(book) + '/content/cover.png)"></a>');
        };

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
        };

        this.setRemoteBooks = function (books) {
            this.remoteBooks = books;
        };

        this.setStorageBooks = function (books) {
            this.storageBooks = books;
        };

        this.hideAllScreens = function () {
            for (var i in this.screens) {
                this.screens[i].hide();
            }
        };

        this.init();
    }
    ;

$(function () {
    app = new App();
});