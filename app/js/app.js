"use strict";
var app;
var App = function () {
        var
            fs = require('fs'),
            path = require('path'),
            request = require("request"),
            appDir = process.cwd() + '/app/',
            booksDir = appDir + 'books/cache/',
            cacheDir = "app://ebook/app/books/cache/",
            url = "http://textbooks-demo.it-attractor.net/",
            $this = this;

        this.screens = {};
        this.modal = $('#modal');
        this.bookTitle = $('.book-title');
        this.bookAuthors = $('.book-authors');
        this.bookISBN = $('.book-isbn');
        this.bookDownload = $('.download');

        this.screens.loading = new Screen($('#loading.screen'));
        this.screens.shelf = new Screen($('#shelf.screen'));
        this.screens.uploading = new Screen($('#uploading.screen'));

        this.storageList = $('.storageList');
        this.remoteList = $('.remoteList');

        this.storageBooks = [];
        this.remoteBooks = [];

        this.getStorageBookList = function () {
            var filterFn = require('unzip.js');

            filterFn(appDir, function (err, list) {
                if (!err) {
                    $this.setStorageBooks(list);
                    $this.drawShelfs();
                }
                else console.log('error: ' + err)
            });
        };

        this.readBook = function (bookSlug) {
            var dirPath = cacheDir + bookSlug;
            var filePath = booksDir + bookSlug + '/index.html';
            var dirHasIndexHtml = fs.existsSync(filePath);

            if (dirHasIndexHtml) window.location.assign(dirPath + "/index.html");
            else readAnotherFormat(booksDir + bookSlug);

            function readAnotherFormat(dirPath) {
                var os = require('os'),
                    exec = require('child_process').exec,
                    runOnWindows = 'start ' + path.normalize(process.cwd() + '/viewer/STDUViewerApp.exe'),
                    runOnLinux = 'xdg-open',
                    runOnOSx = 'open';

                dirPath = path.normalize(dirPath);
                console.log(dirPath);

                var OSAppsTable = {
                    'win32': runOnWindows,
                    'win64': runOnWindows,
                    'linux': runOnLinux,
                    'linux2': runOnLinux,
                    'darwin': runOnOSx
                };

                fs.readdir(dirPath, function (err, files) {
                    files
                        .map(function (file) {return path.join(dirPath, file);})
                        .filter(function (file) {return fs.statSync(file).isFile() & path.basename(file) != 'book.info';})
                        .forEach(function (file) {
                                     readAnotherFormat(file);
                                     console.log(file)
                                 });
                });

                function readAnotherFormat(file) {
                    console.log(file);
                    exec(OSAppsTable[os.platform()] + ' ' + file,
                         function (error, stdout, stderr) {
                             console.log('stdout: ' + stdout);
                             console.log('stderr: ' + stderr);
                             if (error !== null) {
                                 console.log('exec error: ' + error);
                             }
                         });
                }
            }
        };

        this.getRepositoryBookList = function () {
            request({url: url + 'api/books', json: true}, function (error, response, body) {
                if (!error && response.statusCode === 200) {
                    body = JSON.stringify(body);
                    var book = JSON.parse(body).books;
                    $this.setRemoteBooks(book);
                    $this.drawShelfs();
                }
                else console.log('connection error')
            });
        };

        this.downloadBook = function (bookHref) {
            var fileUrl = url + 'books/' + bookHref + '.etb',
                savingDir = path.normalize(appDir + 'books/');
            console.log('download started');

            require('download.js')(fileUrl, savingDir, function (err) {
                if (!err) $this.getStorageBookList();
            });

            $this.drawShelfs();
            $this.switchScreen('shelf');
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

            setTimeout(function () {
                $this.getStorageBookList();
                $this.getRepositoryBookList();
                $this.switchScreen('shelf');
            }, 2000);

        };

        this.drawShelfs = function () {
            $this.renderStorageBooks();
            $this.renderRemoteBooks();
        };

        this.switchScreen = function (screen) {
            this.hideAllScreens();
            this.screens[screen].show();
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
                shelf.find('.book-list').append(this.createStorageBook((this.storageBooks[i])));
            }
            $('.storageList .book').click(function () {
                $this.readBook($(this).attr('href'));
                return false;
            });
        };

        this.renderRemoteBooks = function () {
            var $remoteList = $('.remoteList');
            $remoteList.html('');
            var shelf = this.createShelf();
            $remoteList.append(shelf);
            for (var i = 0, j = 0; i < this.remoteBooks.length; i++, j++) {
                if (j == Math.round((this.screens.shelf.width - 126) / 65)) {
                    shelf = this.createShelf();
                    $remoteList.append(shelf);
                    j = 0;
                }
                this.createRemoteBook(this.remoteBooks[i], i, function (response) {
                    shelf.find('.book-list').append(response);
                });
            }
        };
        $this.remoteList.on('click', '.book', function () {
            var book = $this.remoteBooks[$(this).attr('data-book-id')];
            $('#modal').modal('show');
            $('.book-title').html('<strong>Название: </strong>' + book.title);
            $this.bookAuthors.html('<strong>Авторы: </strong>' + book.authors);
            $this.bookISBN.html('<strong>ISBN: </strong>' + book.ISBN);
            $this.bookDownload.attr('href', book.slug);
            return false;
        });
        $this.modal.on('click', '.download', function () {
            var link = $(this).attr('href');
            $this.switchScreen('uploading');
            setTimeout(function () {
                console.log('yeah');
                $this.downloadBook(link);
            }, 500);
        });

        this.createStorageBook = function (book) {
            book.slug = path.basename(book);
            return $('<a title="Открыть книгу" href="' + path.basename(book) +
                         '" class="book" style="background-image: url(' +
                         cacheDir + path.basename(book) + '/content/cover.png)"></a>');
        };

        this.createRemoteBook = function (book, id, callback) {
            var filePath = url + 'tmp/' + book.slug + '/content/cover.png';
            var defaultFilePath = 'app://reader/app/img/empty-book.png';

            function createBookLink(path) {
                callback($('<a title="Скачать книгу" href="#"' +
                               'class="book remote-book" ' +
                               'data-book-id="' + id + '" style="background-image: url('
                               + path + ')"></a>'));
            }

            request(filePath, function (error, response) {
                var fileDoesntExist = response.statusCode == 404;
                if (fileDoesntExist) {
                    createBookLink(defaultFilePath);
                }
                else {
                    createBookLink(filePath);
                }
            });
        };


        this.createShelf = function () {
            var id = Math.ceil(Math.random() * 100000);
            return $(
                    '<div class="shelf" id="' + id + '">' +
                    '<div class="middle-side">' +
                    '<div class="book-list"></div>' +
                    '</div>' +
                    '</div>'
            );
        }
        ;
        this.updateProgress = function (currentSize, allSize) {
            if (currentSize == allSize) {
                this.drawShelfs();
            }
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