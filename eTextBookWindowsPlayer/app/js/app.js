"use strict";

var app;
var App = function () {
        var
            fs = require('fs'),
            path = require('path'),
            request = require("request"),
            appDir = process.cwd() + '/app/',
            booksDir = appDir + 'books/cache/',
            archiveDir = path.normalize(appDir + 'books/'),
            cacheDir = "app://ebook/app/books/cache/",
            url = "http://textbooks-demo.it-attractor.net/",
            $this = this, currentBook;

        this.screens = {};
        this.modal = $('#modal');
        this.messageBar = $('#messageBar');
        this.bookAuthors = $('.book-authors');
        this.bookDownload = $('.download');

        this.screens.loading = new Screen($('#loading.screen'));
        this.screens.shelf = new Screen($('#shelf.screen'));
        this.screens.uploading = $('#uploading.screen');

        this.storageList = $('.storageList');
        this.remoteList = $('.remoteList');

        this.storageBooks = [];
        this.remoteBooks = [];

        this.unzipAndGetStorageBooksList = function (callback) {
            $this.messageBar.html('Подождите, идёт распаковка книг');
            $this.messageBar.show();
            require('unzip.js')(appDir, function (err, list) {
                if (!err) {
                    $this.setStorageBooks(list);
                    $this.drawShelfs();
                    $this.messageBar.hide();
                    if (typeof callback == "function") callback(null);
                }
                else {
                    $this.messageBar.html('Произошла ошибка: ' + err);
                    if (typeof callback == "function") callback(err);
                }
            });
        };

        this.readBook = function (bookSlug) {
            var dirPath = booksDir + bookSlug;
            var bookUrl = cacheDir + bookSlug;
            var dirHasIndexHtml = fs.existsSync(dirPath + '/index.html');

            if (dirHasIndexHtml) window.location.assign(bookUrl + "/index.html");
            else require('readAnotherFormat')(dirPath, function (err) {
                if (err) console.log("exception couldn't read format");
            });
        };

        this.getRepositoryBookList = function () {
            request({url: url + 'api/books', json: true}, function (error, response, body) {
                if (!error && response.statusCode === 200) {
                    body = JSON.stringify(body);
                    var books = JSON.parse(body).books;
                    $this.setRemoteBooks(books);
                    $this.drawShelfs();
                }
                else {
                    $('.modal-body').html("Нет соединения с сервером");
                    $('#modal').modal('show');
                }
            });
        };

        this.showAvailableUpdates = function (booksArray) {
            for (var i in booksArray) {
                var bookName = booksArray[i].slug.split('-');
                var currentBooksID = bookName[0] + '-' + bookName[1];
                if (bookName.length > 2) {
                    var items = document.getElementsByTagName('a');
                    for (var j = 0; j < items.length; j++) {
                        var item = items[j];
                        var re = new RegExp(currentBooksID);
                        if (re.test(item.getAttribute('href')) && item.getAttribute('href') != booksArray[i].slug) {
                            item.setAttribute("class", "book has-update");
                            item.setAttribute("checkSum", booksArray[i].md5);
                            item.setAttribute('update-link', booksArray[i].slug);
                        }
                    }
                }
            }
        };

        this.downloadBook = function (bookHref, checksum, callback) {
            var fileName = bookHref + '.etb';
            var fileUrl = url + 'books/' + bookHref + '.etb',
                currentState = 0,
                downloadedState = '',
                temporaryName = path.normalize(archiveDir + 'downloading-' + fileName),
                savingName = path.normalize(archiveDir + fileName),
                file = fs.createWriteStream(temporaryName),
                http = require('http');

            $this.showProgress(currentState);

            http.get(fileUrl, function (res) {
                var len = parseInt(res.headers['content-length'], 10);

                res.on('data', function (data) {
                    file.write(data);
                    currentState += data.length;
                    downloadedState = (100.0 * currentState / len).toFixed() + "%";
                    $this.showProgress(downloadedState);
                })
                    .on('end', function () {
                        file.end();
                        $this.showProgress(downloadedState);
                        if (checksum != undefined && checksum.length != 0) {
                            require('calculateCheckSumOfFileAndCheck')
                            (temporaryName, checksum, function (checksumIsOK) {
                                if (checksumIsOK) {
                                    fs.rename(temporaryName, savingName, function (err) {
                                        if (err) throw err;
                                        else $this.unzipAndGetStorageBooksList();
                                        if (typeof callback == "function") callback(null);
                                    });
                                }
                                else {
                                    fs.unlink(temporaryName, function (err) {
                                        if (err) throw err;
                                        if (typeof callback == "function") callback(err);
                                    });
                                    $('.modal-body').html("Произошла ошибка при загрузке книги");
                                    $this.modal.modal('show');
                                    if (typeof callback == "function") callback(err);
                                }
                            })
                        } else
                            fs.rename(temporaryName, savingName, function (err) {
                                if (err) throw err;
                                else $this.unzipAndGetStorageBooksList();
                                if (typeof callback == "function") callback(null);
                            });
                        if (typeof callback == "function") callback(null);
                    });
            });

        };

        this.showProgress = function (progress) {
            $this.switchScreen('uploading');
            var $downloadingScreen = $('#uploading');
            $downloadingScreen.css({backgroundImage: url + currentBook.cover});
            $('.book-title').html(currentBook.title);

            var progressBarParent = document.getElementById('progressBar'),
                progressBar = document.getElementsByClassName('progress-bar');
            if (progress == 0) {
                progressBarParent.style.display = 'block';
                progressBar[0].style.width = 0;
                progressBar[0].innerHTML = "Загружено 0%";
            }
            else if (progress == '100%') {
                $this.switchScreen('shelf');
                progressBarParent.style.display = 'none';
            }
            else {
                progressBar[0].style.width = progress;
                progressBar[0].innerHTML = "Загружено " + progress;
            }
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
            $this.unzipAndGetStorageBooksList(function (err) {
                if (!err) {
                    $this.getRepositoryBookList();
                    $this.switchScreen('shelf');
                }
                else {
                    $('.modal-body').html("Ошибка загрузки книг");
                    $this.modal.modal('show');
                }
            });
        }();

        this.drawShelfs = function () {
            $this.renderStorageBooks();
            $this.renderRemoteBooks();
        };

        this.switchScreen = function (screen) {
            this.hideAllScreens();
            this.screens[screen].show();
        };


        this.renderStorageBooks = function () {
            $this.storageList.html('');
            var shelf = this.createShelf();
            $this.storageList.append(shelf);
            for (var i = 0, j = 0; i < $this.storageBooks.length; i++, j++) {
                if (j == Math.round(($this.screens.shelf.width - 126) / 65)) {
                    shelf = this.createShelf();
                    $('.storageList').append(shelf);
                    j = 0;
                }
                shelf.find('.book-list').append($this.createStorageBook(($this.storageBooks[i])));
            }
            $('.storageList .book').click(function () {

                var bookName = $(this).attr('href');
                if ($(this).hasClass('has-update')) {
                    if (!confirm('Доступна новая версия книги. Загрузить обновленную версию?')) {
                        $this.readBook(bookName);
                    } else {
                        $this.downloadBook($(this).attr('update-link'), $(this).attr('checksum'), function () {
                            $this.deleteBook(bookName)
                        })
                    }
                } else {
                    $this.readBook(bookName);
                }
                return false;
            });
        };

        this.deleteBook = function (bookName) {
            var archiveName = path.normalize(archiveDir + bookName + '.etb');
            var booksDirectoryName = path.normalize(booksDir + bookName);
            if (fs.existsSync(archiveName)) fs.unlink(archiveName);
            if (fs.existsSync(booksDirectoryName)) $this.deleteFolderRecursive(booksDirectoryName);
        };

        this.renderRemoteBooks = function () {
            $this.showAvailableUpdates($this.remoteBooks);
            var $remoteList = $('.remoteList');
            $remoteList.html('');
            var shelf = this.createShelf();
            $remoteList.append(shelf);
            for (var i = 0, j = 0; i < $this.remoteBooks.length; i++, j++) {
                if (j == Math.round(($this.screens.shelf.width - 126) / 65)) {
                    shelf = this.createShelf();
                    $remoteList.append(shelf);
                    j = 0;
                }
                $this.createRemoteBook($this.remoteBooks[i], i, function (response) {
                    shelf.find('.book-list').append(response);
                });
            }
        };

        $this.remoteList.on('click', '.book', function () {
            currentBook = $this.remoteBooks[$(this).attr('data-book-id')];
            $('.book-title').html('<strong>Название: </strong>' + currentBook.title);
            $this.bookAuthors.html('<strong>Авторы: </strong>' + currentBook.authors);
            $this.bookDownload.attr('href', currentBook.slug);
            $this.modal.modal('show');
            return false;
        });
        $this.modal.on('click', '.download', function () {
            var link = currentBook.slug,
                checksum = currentBook.md5;

            $this.modal.modal('hide');
            setTimeout(function () {
                $this.downloadBook(link, checksum);
            }, 500);
        });

        this.createStorageBook = function (book) {
            var bookSlug = path.basename(book);
            return $('<a title="Открыть книгу" href="' + bookSlug +
            '" class="book" style="background-image: url(' +
            cacheDir + bookSlug + '/content/cover.png)"></a>');
        };

        this.createRemoteBook = function (book, id, callback) {

            (function getBackgroundPathAndCallBookLinkCreator() {
                var filePath = url + 'tmp/' + book.slug + '/content/cover.png';
                var defaultFilePath = 'app://reader/app/img/empty-book.png';
                request(filePath, function (error, response) {
                    var fileDoesntExist = response.statusCode == 404;
                    if (fileDoesntExist) {
                        createBookLink(defaultFilePath);
                    }
                    else {
                        createBookLink(filePath);
                    }
                });
            }());


            function createBookLink(bookBackground) {
                callback($('<a title="Скачать книгу" href="#"' +
                'class="book remote-book" ' +
                'data-book-md5="' + book.md5 + '"' +
                'data-book-id="' + id + '" style="background-image: url('
                + bookBackground + ')"></a>'));
            }

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
        };

        this.deleteFolderRecursive = function (path) {
            if (fs.existsSync(path)) {
                fs.readdirSync(path).forEach(function (file) {
                    var curPath = path + "/" + file;
                    if (fs.lstatSync(curPath).isDirectory()) { // recurse
                        $this.deleteFolderRecursive(curPath);
                    } else { // delete file
                        fs.unlinkSync(curPath);
                    }
                });
                fs.rmdirSync(path);
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

    }
    ;

$(function () {
    app = new App();
});