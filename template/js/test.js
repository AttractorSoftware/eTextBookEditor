var eTextBookWidgetTest = eTextBookWidget.extend({
    defaults: {
        slug: "test"
        ,title: "Тест"
    }
});

App.eTextBookWidgetRepository.registerWidget(eTextBookWidgetTest);