var eTextBookWidgetTest = eTextBookWidget.extend({
	 defaults: {
		  slug: "test",
		  title: "Тест",
		  templateName: 'testSolutionWidget'
	 },
	 startEdit: function () {
		  for (var i = 0; i < this.cont.find('.task').length; i++) {
				var item = $(this.cont.find('.test-widget .task')[i]);
				item.find('input').val(item.find('view-element').html());
				item.find('select').val(item.attr('value'));
		  }
	 },
	 activate: function () {
		  this.cont = this.editCont.find('.test-widget');
		  this.addQuestionWithChoice(this.cont);
		  this.binds();
	 },
	 addQuestionWithChoice: function (obj) {
		  obj.append(App.eTextBookTemplate.getTemplate('testWidgetTask'));
		  this.addChoice(obj.find('.add-question .choices:last'));
	 },
	 addChoice: function (obj) {
		  obj.append(App.eTextBookTemplate.getTemplate('addChoice'));
//		  generateSelectFromChoices().call(obj);
		  /*
			function generateSelectFromChoices() {
			var values = this.contentContainer.find('.choices-list').html().split(', ');
			var options = '<option></option>';
			for(var i = 0; i < values.length; i++) {
			if(values[i] != '') {
			options += '<option value="' + values[i] + '">' + values[i] + '</option>';
			}
			} return '<select class="not-saved">' + options + '</select>';
			}
			*/
	 },
	 binds: function () {
		  var $this = this;
		  const ENTER_KEY = 13;

		  $('.test-widget')
			  .on('click', '.question .add', function () {
					addElement.call(this);
			  })
			  .on('click', '.add-choice .add', function () {
					addElement.call(this);
			  })
			  .on('keyup', '.question input', function (e) {
					if(e.which == ENTER_KEY) addElement.call(this);
			  })
			  .on('keyup', '.add-choice input', function (e) {
					if(e.which == ENTER_KEY) addElement.call(this);
			  })
			  .on('click', '.glyphicon-remove', function () {
					$(this).closest('edit-element').remove();
			  });

		  function addElement() {
				var closestEditElement = $(this).closest('edit-element');
				var closestGlyphIcon = closestEditElement.find('.glyphicon').first();

				if(closestGlyphIcon.hasClass("add")) {
					 if(closestEditElement.hasClass("add-question"))
						  $this.addQuestionWithChoice($(this).closest(".test-widget"));
					 else if(closestEditElement.hasClass("add-choice"))
						  $this.addChoice($(this).closest(".choices"));
					 closestGlyphIcon.addClass('glyphicon-remove').removeClass('add glyphicon-plus');
				}
		  }

	 }

});

App.eTextBookWidgetRepository.registerWidget(eTextBookWidgetTest);