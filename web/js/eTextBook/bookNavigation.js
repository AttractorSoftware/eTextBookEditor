var NavigationController = function () {
    this.iframe = $('#book');
    this.bookName = $('.book-name').html();

    var $this = this, activeChapter, activeChapterIndex, scrollPos,
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
        $this.iframe.attr('src', storage.local.chapters[storage.local.activeChapter]);
    };

    this.resizeIframeByContent = function (iframe) {
        iframe.style.height = iframe.contentWindow.document.body.offsetHeight + 'px';
    };

    this.iframe.on('load', function () {
        activeChapter = $this.iframe.attr('src');
        activeChapterIndex = storage.local.chapters.indexOf(activeChapter);
        storage.local.activeChapter = activeChapterIndex;

        $this.resizeIframeByContent(this);
        $('.active').removeClass('active');
        $('a[href="' + activeChapter + '"]').closest('.chapter').addClass('active');
        window.scrollTo(0, storage.local.scrollPositions[activeChapterIndex]);
    });

    $(window).scroll(function () {
        scrollPos = getScrollTop();
        storage.local.scrollPositions[activeChapterIndex] = scrollPos;
    });

    $('#show-book-summary').click(function () {
        $('.book').toggleClass('with-summary');
    });


    $('.exercise-link').click(function () {
        var self = $(this);
        var link = self.closest('.chapter').children('.chapter-link').attr('href');
        if ($this.iframe.attr('src') !== link) {
            $this.iframe.attr('src', link);
            document.getElementsByTagName('iframe')[0].onload = function () {
                scrollToExercise();
            };
        } else {
            scrollToExercise();
        }

        function scrollToExercise() {
            var $tr = $this.iframe.contents().find("#" + self.attr('href'));
            if (typeof ($tr.offset()) != 'undefined') window.scrollTo(0, $tr.offset().top);
        }

        return false;
    });
    $('.chapter-link').click(function () {
        var link = $(this).attr('href');
        if ($this.iframe.attr('src') !== link) {
            $this.iframe.attr('src', link);
        }
        return false;
    });

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

    if (!storage.local.chapters) {
        $this.setLocalStorage();
    }
    $this.openLastReadingPlace();

};
App.NavigationController = new NavigationController();