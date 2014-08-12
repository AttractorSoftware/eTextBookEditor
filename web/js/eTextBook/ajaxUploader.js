var AjaxUploader = function(config) {
    var $this = this;
    this.config = config;
    this.fileInput = config.fileInput;
    this.files = this.fileInput[0].files;
    this.uploadAction = this.fileInput.attr('upload-action');
    this.reader = new FileReader();
    this.afterLoad = config.afterLoad;
    this.uploadAction = this.fileInput.attr('upload-action');
    this.afterUpload = config.afterUpload;

    this.init = function() {
        this.fileInput.bind('change', this.select);
    }

    this.select = function() {
        $this.loadContent();
        $this.upload();
    }

    this.loadContent = function() {
        $this.getAsDataUrl(function() {
            $this.getAsText(function(){
                $this.getAsArrayBuffer($this.afterLoad);
            });
        });
    }

    this.getAsText = function(callback) {
        this.reader.readAsText($this.files[0]);
        this.reader.onload = function(e) {
            $this.textResult = e.target.result;
            callback();
        }
    }

    this.getAsDataUrl = function(callback) {
        this.reader.readAsDataURL($this.files[0]);
        this.reader.onload = function(e) {
            $this.dataUrlResult = e.target.result;
            callback();
        }
    }

    this.getAsArrayBuffer = function(callback) {
        this.reader.readAsArrayBuffer($this.files[0]);
        this.reader.onload = function (e) {
            $this.arrayBufferResult = e.target.result;
            callback();
        }
    }

    this.upload = function() {
        xhr = new XMLHttpRequest();
        xhr.open("post", this.uploadAction, true);
        var data = new FormData();
        data.append($this.fileInput.attr('name'), this.files[0]);
        xhr.send(data);
        xhr.onreadystatechange = function() {
            if (xhr.readyState==4 && xhr.status==200) {
                $this.uploadResult = xhr.responseText;
                $this.afterUpload();
            }
        }
    }

    this.init();
}