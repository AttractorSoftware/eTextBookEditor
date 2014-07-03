var html5Player = function(target) {

    var $this = this;

    this.target = $(target);
    this.width = parseInt(this.target.width());
    this.height = parseInt(this.target.height());
    this.marginBottom = parseInt(this.target.css('marginBottom').split('px')[0]);


    this.controls = $(
        '<div class="html5-controls">' +
            '<div class="play glyphicon glyphicon-play"></div>' +
            '<div class="pause glyphicon glyphicon-pause"></div>' +
            '<div class="play-bar"><div class="position"></div></div>' +
            '<div class="time">0:00</div>' +
            '<div class="mute glyphicon glyphicon-volume-down"></div>' +
            '<div class="muted glyphicon glyphicon glyphicon-volume-off"></div>' +
            '<div class="volume">' +
                '<div class="item"></div>' +
                '<div class="item"></div>' +
                '<div class="item"></div>' +
                '<div class="item"></div>' +
                '<div class="item"></div>' +
            '</div>' +
            '<div class="full-screen glyphicon glyphicon-fullscreen"></div>' +
        '</div>'
    );

    this.init = function() {
        this.appendControls();
        this.activateControls();
        this.listenEvents();
    }

    this.listenEvents = function() {
        this.target.bind('ended', function(){ $this.pause(); });
    }

    this.appendControls = function() {
        this.target.removeAttr('controls');
        this.target.after(this.controls);

        this.controlHeight = parseInt(this.controls.height());

        this.controls.css({
            marginTop: -this.controlHeight-this.marginBottom - 5,
            width: this.width - 10
        });
    }

    this.activateControls = function() {
        this.controls.find('.play').bind('click', function(){ $this.play(); });
        this.controls.find('.pause').bind('click', function(){ $this.pause(); });
        this.controls.find('.mute').bind('click', function(){ $this.mute(); });
        this.controls.find('.muted').bind('click', function(){ $this.mute(); });
        this.controls.find('.full-screen').bind('click', function(){ $this.fullScreen(); })
    }

    this.fullScreen = function() {
        if(this.target[0].requestFullscreen) {
            this.target[0].requestFullscreen();
        } else {
            if(this.target[0].mozRequestFullScreen) {
                var res = this.target[0].mozRequestFullScreen();
            } else {
                if(this.target[0].webkitRequestFullscreen) {
                    this.target[0].webkitRequestFullscreen();
                }
            }
        }
    }

    this.mute = function() {
        var muted = this.target[0].muted;
        if(muted) {
            this.controls.find('.mute').show();
            this.controls.find('.muted').hide();
            this.target[0].muted = false;
        } else {
            this.controls.find('.mute').hide();
            this.controls.find('.muted').show();
            this.target[0].muted = true;
        }
    }

    this.play = function() {
        console.debug('q');
        this.target[0].play();
        this.controls.find('.play').hide();
        this.controls.find('.pause').show();
        this.startCurrentTimeDisplayUpdate();
    }

    this.pause = function() {
        this.target[0].pause();
        this.controls.find('.play').show();
        this.controls.find('.pause').hide();
        this.stopCurrentTimeDisplayUpdate();
    }

    this.startCurrentTimeDisplayUpdate = function() {
        this.currentTimeInterval = setInterval(function(){
            $this.updateCurrentTime();
        }, 10);
    }

    this.stopCurrentTimeDisplayUpdate = function() {
        clearInterval(this.currentTimeInterval);
    }

    this.updateCurrentTime = function() {
        var currentTime = this.target[0].currentTime;
        var duration = this.target[0].duration;
        this.controls.find('.time').html(this.secondsToString(currentTime));
        this.controls.find('.position').css({ width: currentTime * 100 / duration + '%'});
    }

    this.secondsToString = function(seconds) {
        var seconds = Math.ceil(seconds);

        if(seconds < 60 ) {
            return seconds < 10 ? '0:0' + seconds : '0:' + seconds;
        } else {
            var minutes = Math.ceil(seconds / 60);
            var displayMinutes = minutes < 10 ? '0' + minutes : minutes;
            var partSeconds = seconds - (minutes * 60);
            var displaySeconds = partSeconds < 10 ? '0' + partSeconds : partSeconds;
            return displayMinutes + ':' + displaySeconds;
        }
    }

    this.init();
}

$(function() {
    setTimeout(function(){
        for(var i = 0; i < $('video').length; i++) {
            new html5Player($('video')[i]);
        }
    }, 1000);
});