var eTextBookWidgetQuestion = eTextBookWidget.extend({
	 defaults: {
		  slug: "question",
		  title: Translator._("Текстовое задание"),
		  templateName: 'questionWidget'
         ,ico: '<span class="glyphicon glyphicon-font"></span>'
	 },
	 finishEdit: function () {
		  this.editCont.find('view-element').html(this.editElement.find('textarea').code());
	 },
	 startEdit: function () {
         this.editElement.find('textarea').code(this.editCont.find('view-element').html());
	 },
	 activate: function () {
         this.editElement = $('<edit-element><label>Текст задания:</label><textarea></textarea></edit-element>');
		  this.editCont.append(this.editElement);
         this.editElement.find('textarea').summernote(App.eTextBookEditor.toolbarConfig);
	 },
	 viewActivate: function () {}

});

App.eTextBookWidgetRepository.registerWidget(eTextBookWidgetQuestion);