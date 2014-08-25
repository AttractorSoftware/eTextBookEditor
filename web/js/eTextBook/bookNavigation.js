$(document).ready(function () {
    var $iframe = $('#book');
    this.loadIframe = function () {
        var $firstChapter = $('.chapter-link:first');
        var $firstChapterLink = $firstChapter.attr('href');
        $firstChapter.closest('.chapter').addClass('active');
        $iframe.attr('src', $firstChapterLink);
    };

    this.loadIframe();
    var bookName = $('.book-name').html(),
        activeChapter = $('.chapter.active a').attr('href'),
        scrollPos = getScrollTop(),
        storage = new ObjectStorage(bookName);
    if (!storage.local.chapter) {
        storage.local = {
            chapter: activeChapter,
            scroll: scrollPos
        };
    }
    else console.log(storage.local);

    $(window).scroll(function () {
        scrollPos = getScrollTop();
        storage.local.scroll = scrollPos;
    });

    $('#show-book-summary').click(function () {
        $('.book').toggleClass('with-summary');
    });
    $('.exercise-link').click(function () {
        var $this = $(this);
        var link = $this.closest('.chapter').children('.chapter-link').attr('href');
        if ($iframe.attr('src') !== link) {
            toggleChapterToActive.call(this);
            $iframe.attr('src', link);
            document.getElementsByTagName('iframe')[0].onload = function () {
                scrollToExercise();
            };
        } else {
            scrollToExercise();
        }

        function scrollToExercise() {
            var $tr = $iframe.contents().find("#" + $this.attr('href'));
            if (typeof ($tr.offset()) != 'undefined') $(window).scrollTop($tr.offset().top);
        }

        return false;
    });
    $('.chapter-link').click(function () {
        var link = $(this).attr('href');
        if ($iframe.attr('src') !== link) {
            toggleChapterToActive.call(this);
            $iframe.attr('src', link);
            $(window).scrollTop(0);
        }
        return false;
    });

    function toggleChapterToActive() {
        $('.active').removeClass('active');
        $(this).closest('.chapter').addClass('active');
        activeChapter = $('.chapter.active a').attr('href');
        storage.local.chapter = activeChapter;
    }

    function getScrollTop() {
        if (typeof pageYOffset != 'undefined') {
            return pageYOffset;
        }
        else {
            var B = document.body;
            var D = document.documentElement;
            D = (D.clientHeight) ? D : B;
            return D.scrollTop;
        }
    }
});
var ObjectStorage = function ObjectStorage(name, duration) {
    var self,
        name = name || '_objectStorage',
        defaultDuration = 5000;

    if (ObjectStorage.instances[ name ]) {
        self = ObjectStorage.instances[ name ];
        self.duration = duration || self.duration;
    } else {
        self = this;
        self._name = name;
        self.duration = duration || defaultDuration;
        self._init();
        ObjectStorage.instances[ name ] = self;
    }

    return self;
};
ObjectStorage.instances = {};
ObjectStorage.prototype = {
    // type == local || session
    _save: function (type) {
        var stringified = JSON.stringify(this[ type ]),
            storage = window[ type + 'Storage' ];
        if (storage.getItem(this._name) !== stringified) {
            storage.setItem(this._name, stringified);
        }
    },

    _get: function (type) {
        this[ type ] = JSON.parse(window[ type + 'Storage' ].getItem(this._name)) || {};
    },

    _init: function () {
        var self = this;
        self._get('local');
        self._get('session');

        (function callee() {
            self.timeoutId = setTimeout(function () {
                self._save('local');
                callee();
            }, self._duration);
        })();

        window.addEventListener('beforeunload', function () {
            self._save('local');
            self._save('session');
        });
    },
    timeoutId: null,
    local: {},
    session: {}
};