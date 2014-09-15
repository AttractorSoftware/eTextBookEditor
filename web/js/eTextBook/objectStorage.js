var ObjectStorage = function ObjectStorage(name, duration) {
    var _this,
        name = name || '_objectStorage',
        defaultDuration = 5000;

    if (ObjectStorage.instances[ name ]) {
        _this = ObjectStorage.instances[ name ];
        _this.duration = duration || _this.duration;
    } else {
        _this = this;
        _this._name = name;
        _this.duration = duration || defaultDuration;
        _this._init();
        ObjectStorage.instances[ name ] = _this;
    }

    return _this;
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

        (function callee() {
            self.timeoutId = setTimeout(function () {
                self._save('local');
                callee();
            }, self._duration);
        })();

        window.addEventListener('beforeunload', function () {
            self._save('local');
        });
    },
    timeoutId: null,
    local: {}
};
