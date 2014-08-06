var eTextBookWidgetQuestion = eTextBookWidget.extend({
	 defaults: {
		  slug: "question",
		  title: "Текстовое задание",
		  templateName: 'questionWidget'
         ,ico: '<span class="glyphicon glyphicon-font"></span>'
	 },
	 finishEdit: function () {
		  this.editCont.find('view-element').html(App.eTextBookUtils.parseTextBlockToHtml(this.editCont.find('textarea').val()));
	 },
	 startEdit: function () {
		  this.editCont.find('textarea').val(App.eTextBookUtils.parseTextBlockFromHtml(this.editCont.find('view-element').html()));
	 },
	 activate: function () {
		  this.editCont.append('<edit-element><label>Текст задания:</label><textarea></textarea></edit-element>');
	 },
	 viewActivate: function () {}

});

App.eTextBookWidgetRepository.registerWidget(eTextBookWidgetQuestion);