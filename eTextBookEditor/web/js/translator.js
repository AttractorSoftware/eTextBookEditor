var Translator = {
    translateList: [],
    addTranslateItem: function (stringName, stringValue) {
        this.translateList.push({
            name: stringName,
            value: stringValue
        });
    },
    _: function (stringName) {
        for (var i = 0; i < this.translateList.length; i++) {
            if (this.translateList[i].name == stringName) {
                return this.translateList[i].value;
            }
        }
        return stringName;
    }
};