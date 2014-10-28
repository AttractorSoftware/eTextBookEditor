var NavigationController = function () {
    this.bookBody = $("#book-body");
    this.bookName = $('.book-name').html();

    var _this = this, activeChapter, activeChapterIndex, scrollPos,
        storage = new ObjectStorage(this.bookName);

    this.setLocalStorage = function () {
        storage.local = {
            activeChapter: 0,
            chapters: [],
            scrollPositions: []
        };
        $('.chapter-link').each(function () {
            storage.local.chapters.push($(this).attr('href'));
        });
    };

    this.openLastReadingPlace = function () {
        var link = storage.local.chapters[storage.local.activeChapter];
        this.bookBody.attr('data-url', link);
        this.bookBody.load(link + ' e-text-book', this.onAjaxLoadComplete);
    };

    this.onAjaxLoadComplete = function () {
        activeChapter = _this.bookBody.attr('data-url');
        activeChapterIndex = storage.local.chapters.indexOf(activeChapter);
        storage.local.activeChapter = activeChapterIndex;

        $('.active').removeClass('active');
        $('a[href="' + activeChapter + '"]').closest('.chapter').addClass('active');
        window.scrollTo(0, storage.local.scrollPositions[activeChapterIndex]);
        _this.activateWidgets();
        AniJS.run();
    };

    this.activateWidgets = function() {
        templateFormat.setRootTag($('e-text-book'));
        templateFormat.parseData();
        templateFormat.reDraw();

        for(var i = 0; i < $('body').find('widget').length; i++) {
            var widgetCont = $($('body').find('widget')[i]);
            var widget = App.eTextBookWidgetRepository.getWidgetBySlug(widgetCont.attr('widget-slug'));
            widget = new widget();
            widget.contentContainer = widgetCont.find('widget-content');
            widget.viewActivate();
        }

        for(var i = 0; i < $('video, audio').length; i++) {
            new html5Player($('video, audio')[i]);
        }
    };

    $(window).scroll(function () {
        scrollPos = getScrollTop();
        storage.local.scrollPositions[activeChapterIndex] = scrollPos;
    });

    $('#show-book-summary').click(function () {
        $('.book').toggleClass('with-summary');
    });

    $('.exercise-link').click(function () {
        var self = $(this);
        var hash = self.attr('href');
        var link = self.closest('.chapter').children('.chapter-link').attr('href');
        if (_this.bookBody.attr('data-url') !== link) {
            _this.bookBody.attr('data-url', link);
            _this.bookBody.load(link + ' e-text-book', function () {
                _this.onAjaxLoadComplete();
                window.location.hash = '#' + hash;
            });
        }
        window.location.hash = '#' + hash;
        return false;
    });
    $('.chapter-link').click(function () {
        var link = $(this).attr('href');
        if (_this.bookBody.attr('data-url') !== link) {
            _this.bookBody.attr('data-url', link);
            _this.bookBody.load(link + ' e-text-book', _this.onAjaxLoadComplete);
        }
        return false;
    });

    function getScrollTop() {
        if (typeof pageYOffset !== 'undefined') {
            return pageYOffset;
        }
        else {
            var B = document.body;
            var D = document.documentElement;
            D = (D.clientHeight) ? D : B;
            return D.scrollTop;
        }
    }

    if (!storage.local.chapters) {
        _this.setLocalStorage();
    }
    _this.openLastReadingPlace();

};

App.NavigationController = new NavigationController();
