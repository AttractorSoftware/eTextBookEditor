var activeInputElement = false;
var range = {start: 0, end: 0};
var bufVal = '';
var bufReplace = '';

$(document).ready(function () {
	 var $charsBlock = $('#charsBlock');

	 $('.book').on('click', '#show-book-summary', function(){
		  $('.book').toggleClass('with-summary');
	 });

/*
	 $( "#book" ).load( "./modules/25-07-14-17-23-49.html", function( response, status, xhr ) {
		  if ( status == "error" ) {
				var msg = "Sorry but there was an error: ";
				$( "#error" ).html( msg + xhr.status + " " + xhr.statusText );
		  }
	 } );
*/


	 $(document).click(function (e) {
		  var $clicked = $(e.target);
		  if($clicked.attr('id') !== 'charsBlock'
			  & $clicked.closest('#charsBlock').size() <= 0
			  & $charsBlock.is(':visible')
			  & $clicked.not('input')
			  && $clicked.not('textarea')) {
				$charsBlock.hide();
		  }
	 });

	 $charsBlock.click(function (e) {
		  var character = $(this).text();
		  if(e.shiftKey) character = character.toUpperCase();
		  setCharacter(character);
	 });


	 function getPressedChar(event) {
		  var char = '';
		  if(event.altKey && event.ctrlKey) {
				switch (event.keyCode) {
					 case 69:
						  char = 'ү';
						  break;
					 case 89:
						  char = 'ң';
						  break;
					 case 74:
						  char = 'ө';
						  break;
				}
		  }
		  if(event.shiftKey) char = char.toUpperCase();
		  setCharacter(char);
	 }

	 function setCharacter(character) {
		  if(!activeInputElement) return false;
		  var newText = '';
		  var value = $(activeInputElement).val();
		  if($.trim(bufReplace) != '') {
				value = value.replace(bufReplace, '');
				$(activeInputElement).val(value);
		  }
		  if(value.length > 0) {
				var startText = value.substr(0, range.start);
				var endText = value.substr(range.end, value.length);
				newText = startText + character + endText;
		  } else newText = character;
		  $(activeInputElement).val(newText);
		  $(activeInputElement).caretPos(parseInt(range.start) + 1, parseInt(range.start) + 1);
		  $(activeInputElement).focus();
		  return false;
	 }

	 $('.desktop').on('scroll', function () {if($charsBlock.is(':visible')) $charsBlock.hide();});


	 $('.container')
		 .on('focus', ':text, textarea', function () {
			  activeInputElement = this;
			  if($.trim(this.value) == '' && $.trim(this.value) != bufVal) bufReplace = bufVal;
			  else bufReplace = '';
			  range = $(activeInputElement).caretPos();
			  var cur_offset = $(this).offset();
			  var pos = {left: '40%', top: cur_offset.top - 110};
			  $charsBlock.hide();
			  if(!$charsBlock.is(':visible')) $charsBlock.css(pos).show();
		 })
		 .on('mousedown', ':text, textarea', function () {bufVal = $.trim(this.value);})
		 .on('click keyup', ':text, textarea', function () {
			  activeInputElement = this;
			  range = $(activeInputElement).caretPos();
			  $charsBlock.show();
		 })
		 .on('keyup', ':text, textarea', getPressedChar);
});

(function ($) {
	 $.fn.caretPos = function (start, end) {
		  var elem = this[0];
		  if(elem) {
				if(typeof start == "undefined") {
					 if(elem.selectionStart) {
						  start = elem.selectionStart;
						  end = elem.selectionEnd;
					 } else if(document.selection) {
						  var val = this.val();
						  var range = document.selection.createRange().duplicate();
						  range.moveEnd("character", val.length);
						  start = (range.text == "" ? val.length : val.lastIndexOf(range.text));

						  range = document.selection.createRange().duplicate();
						  range.moveStart("character", -val.length);
						  end = range.text.length;
					 }
				} else {
					 val = this.val();

					 if(typeof start != "number") start = -1;
					 if(typeof end != "number") end = -1;
					 if(start < 0) start = 0;
					 if(end > val.length) end = val.length;
					 if(end < start) end = start;
					 if(start > end) start = end;

					 elem.focus();

					 if(typeof elem.selectionStart == "number" && elem.selectionStart != start) {
						  elem.selectionStart = start;
						  elem.selectionEnd = end;
					 }
					 else if(document.selection) {
						  range = elem.createTextRange();
						  range.collapse(true);
						  range.moveStart("character", start);
						  range.moveEnd("character", end - start);
						  range.select();

					 }
				}
		  }
		  start = parseInt(start) || 0;
		  end = parseInt(end) || 0;
		  return {start: start, end: end};
	 }
})(jQuery);