var AjaxUploader = function(config) {
    var $this = this;
    this.percentLoaded = 0;

    this.init = function() {
        if(config.autoUpload) {
            this.bindChange();
        }
    };

    this.bindChange = function() {
        config.input.bind('change', function(e) {
            $this.read();
            $this.upload();
        });
    };

    this.read = function() {
        $this.fileReader = new FileReader();
        $this.fileReader.onload = function(event) {
            $this.fileDataUrlContent = event.target.result;
            $.isFunction(config.afterRead) ? config.afterRead() : false;
        };
        $this.fileReader.readAsDataURL($(config.input).prop('files')[0]);
    };

    this.upload = function() {
        var http = new XMLHttpRequest();
        http.upload.addEventListener('progress', function(e) {
            $this.percentLoaded = Math.floor(e.loaded * 100 / e.total) + '%';
            $.isFunction(config.onProgress) ? config.onProgress() : false;
        }, false);

        http.onreadystatechange = function()  {
            if (http.readyState == 4 && http.status == 200) {
                $this.uploadResult = http.responseText;
                $.isFunction(config.afterUpload) ? config.afterUpload() : false;
            }
        };

        var form = new FormData();
        form.append('path', '/');
        form.append(config.input.attr('name'), $(config.input).prop('files')[0]);
        http.open('POST', config.uploadPath);
        http.send(form);
    };

    this.init();
}