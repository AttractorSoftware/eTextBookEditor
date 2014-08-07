$(document).ready(function () {
	 var $charsBlock = $('#charsBlock');
	 var activeInputElement = false;

	 $('#charsBlock span').click(function (e) {
		  var character = $(this).text();
		  if(e.shiftKey) character = character.toUpperCase();
		  setCharacter(character);
	 });

	 function getPressedChar(event) {
		  var char = '';
		  var charTable = {
				69: 'ү',
				89: 'ң',
				74: 'ө'
		  };
		  if(event.altKey && event.ctrlKey) {
				char = charTable[event.keyCode];
		  }
		  if(event.shiftKey) char = char.toUpperCase();
		  setCharacter(char);
	 }

	 function setCharacter(character) {
		  $(activeInputElement).caret(character);
		  return false;
	 }


	 $('.container')
		 .on('focus', ':text, textarea', function () {
			  activeInputElement = this;
			  var cur_offset = $(this).offset();
			  var pos = {left: '40%', top: cur_offset.top - 110};
			  $charsBlock.hide();
			  $charsBlock.css(pos).show();
		 })
		 .on('keyup', ':text, textarea', getPressedChar);

	 $(document).click(function (e) {
		  var $clicked = $(e.target),
			  hideCharsBlock = !(
				  $clicked.attr('id') == 'charsBlock'
					  || $clicked.closest('#charsBlock').size() > 0
					  || $clicked.is('input')
					  || $clicked.is('textarea')
				  );

		  if(hideCharsBlock) {
				$charsBlock.hide();
		  }
	 });

	 $('.desktop').on('scroll', function () {$charsBlock.hide();});

});

(function ($) {

	 var _input = document.createElement('input');

	 var _support = {
		  setSelectionRange: ('setSelectionRange' in _input) || ('selectionStart' in _input),
		  createTextRange: ('createTextRange' in _input) || ('selection' in document)
	 };

	 var _rNewlineIE = /\r\n/g,
		 _rCarriageReturn = /\r/g;

	 var _getValue = function (input) {
		  if(typeof(input.value) !== 'undefined') {
				return input.value;
		  }
		  return $(input).text();
	 };

	 var _setValue = function (input, value) {
		  if(typeof(input.value) !== 'undefined') {
				input.value = value;
		  } else {
				$(input).text(value);
		  }
	 };

	 var _getIndex = function (input, pos) {
		  var norm = _getValue(input).replace(_rCarriageReturn, '');
		  var len = norm.length;

		  if(typeof(pos) === 'undefined') {
				pos = len;
		  }

		  pos = Math.floor(pos);

		  // Negative index counts backward from the end of the input/textarea's value
		  if(pos < 0) {
				pos = len + pos;
		  }

		  // Enforce boundaries
		  if(pos < 0) {
				pos = 0;
		  }
		  if(pos > len) {
				pos = len;
		  }

		  return pos;
	 };

	 var _hasAttr = function (input, attrName) {
		  return input.hasAttribute ? input.hasAttribute(attrName) : (typeof(input[attrName]) !== 'undefined');
	 };

	 var Range = function (start, end, length, text) {
		  this.start = start || 0;
		  this.end = end || 0;
		  this.length = length || 0;
		  this.text = text || '';
	 };

	 Range.prototype.toString = function () {
		  return JSON.stringify(this, null, '    ');
	 };

	 var _getCaretW3 = function (input) {
		  return input.selectionStart;
	 };

	 var _getCaretIE = function (input) {
		  var caret, range, textInputRange, rawValue, len, endRange;

		  // Yeah, you have to focus twice for IE 7 and 8.  *cries*
		  input.focus();
		  input.focus();

		  range = document.selection.createRange();

		  if(range && range.parentElement() === input) {
				rawValue = _getValue(input);

				len = rawValue.length;

				// Create a working TextRange that lives only in the input
				textInputRange = input.createTextRange();
				textInputRange.moveToBookmark(range.getBookmark());

				// Check if the start and end of the selection are at the very end
				// of the input, since moveStart/moveEnd doesn't return what we want
				// in those cases
				endRange = input.createTextRange();
				endRange.collapse(false);

				if(textInputRange.compareEndPoints("StartToEnd", endRange) > -1) {
					 caret = rawValue.replace(_rNewlineIE, '\n').length;
				} else {
					 caret = -textInputRange.moveStart("character", -len);
				}

				return caret;
		  }

		  return 0;
	 };


	 var _getCaret = function (input) {
		  if(!input) {
				return undefined;
		  }

		  // Mozilla, et al.
		  if(_support.setSelectionRange) {
				return _getCaretW3(input);
		  }
		  // IE
		  else if(_support.createTextRange) {
				return _getCaretIE(input);
		  }

		  return undefined;
	 };

	 var _setCaretW3 = function (input, pos) {
		  input.setSelectionRange(pos, pos);
	 };

	 var _setCaretIE = function (input, pos) {
		  var range = input.createTextRange();
		  range.move('character', pos);
		  range.select();
	 };

	 var _setCaret = function (input, pos) {
		  input.focus();

		  pos = _getIndex(input, pos);

		  // Mozilla, et al.
		  if(_support.setSelectionRange) {
				_setCaretW3(input, pos);
		  }
		  // IE
		  else if(_support.createTextRange) {
				_setCaretIE(input, pos);
		  }
	 };

	 var _insertAtCaret = function (input, text) {
		  var curPos = _getCaret(input);

		  var oldValueNorm = _getValue(input).replace(_rCarriageReturn, '');

		  var newLength = +(curPos + text.length + (oldValueNorm.length - curPos));
		  var maxLength = +input.getAttribute('maxlength');

		  if(_hasAttr(input, 'maxlength') && newLength > maxLength) {
				var delta = text.length - (newLength - maxLength);
				text = text.substr(0, delta);
		  }

		  _setValue(input, oldValueNorm.substr(0, curPos) + text + oldValueNorm.substr(curPos));

		  _setCaret(input, curPos + text.length);
	 };

	 $.extend($.fn, {
		  caret: function () {
				var $inputs = this.filter('input, textarea');
				if(typeof arguments[0] === 'string') {
					 var text = arguments[0];
					 $inputs.each(function (_i, input) {
						  _insertAtCaret(input, text);
					 });
				}

				return this;
		  }
	 });

}(window.jQuery || window.Zepto || window.$));