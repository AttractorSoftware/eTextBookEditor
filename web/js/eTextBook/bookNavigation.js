$(document).ready(function () {
    var $iframe = $('#book');
    var $firstModuleLink = $('.chapter-link:first').attr('href');
    $iframe.attr('src', $firstModuleLink);

    $('#show-book-summary').click(function () {
        $('.book').toggleClass('with-summary');
    });
    $('.exercise-link').click(function () {
        var $this = $(this);
        toggleChapterToActive.call(this);
        var link = $this.closest('.chapter').children('.chapter-link').attr('href');
        if ($iframe.attr('src') !== link) {
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
            $iframe.attr('src', link);
            $(window).scrollTop(0);
        }
        toggleChapterToActive.call(this);
        return false;
    });

    function toggleChapterToActive() {
        $('.active').removeClass('active');
        $(this).closest('.chapter').addClass('active');
    }

});
