/*! Rainbow v1.1.8 rainbowco.de | modified by ykiwng @ www.creasty.com */

(function($) {
	var replacements = {},
		replacement_positions = {},
		language_patterns = {},
		bypass_defaults = {},
		CURRENT_LEVEL = 0,
		DEFAULT_LANGUAGE = 0,
		match_counter = 0,
		replacement_counter = 0;

	function _htmlEntities(code) {
		return code.replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/&(?![\w\#]+;)/g, '&amp;');
	}

	function _intersects(start1, end1, start2, end2) {
		return start2 >= start1 && start2 < end1 || end2 > start1 && end2 < end1;
	}

	function _hasCompleteOverlap(start1, end1, start2, end2) {
		return !(start2 == start1 && end2 == end1) && start2 <= start1 && end2 >= end1;
	}

	function _matchIsInsideOtherMatch(start, end) {
		for (var key in replacement_positions[CURRENT_LEVEL]) {
			key = parseInt(key, 10);

			if (_hasCompleteOverlap(key, replacement_positions[CURRENT_LEVEL][key], start, end)) {
				delete replacement_positions[CURRENT_LEVEL][key];
				delete replacements[CURRENT_LEVEL][key];
			}

			if (_intersects(key, replacement_positions[CURRENT_LEVEL][key], start, end)) {
				return true;
			}
		}

		return false;
	}

	function _wrapCodeInSpan(name, code) {
		return '<span class="' + name.replace(/\./g, ' ') + '">' + code + '</span>';
	}

	function _indexOfGroup(match, group_number) {
		var index = 0,
			i;

		for (i = 1; i < group_number; ++i) {
			if (match[i]) {
				index += match[i].length;
			}
		}

		return index;
	}

	function _processPattern(regex, pattern, code, callback)
	{
		var match = regex.exec(code);

		if (!match) {
			return callback();
		}

		++match_counter;

		if (!pattern['name'] && typeof pattern['matches'][0] == 'string') {
			pattern['name'] = pattern['matches'][0];
			delete pattern['matches'][0];
		}

		var replacement = match[0],
			start_pos = match.index,
			end_pos = match[0].length + start_pos,

			processNext = function() {
				var nextCall = function() {
					_processPattern(regex, pattern, code, callback);
				};

				return match_counter % 100 > 0 ? nextCall() : setTimeout(nextCall, 0);
			};

		if (_matchIsInsideOtherMatch(start_pos, end_pos)) {
			return processNext();
		}

		var onMatchSuccess = function(replacement) {
				if (pattern['name']) {
					replacement = _wrapCodeInSpan(pattern['name'], replacement);
				}

				if (!replacements[CURRENT_LEVEL]) {
					replacements[CURRENT_LEVEL] = {};
					replacement_positions[CURRENT_LEVEL] = {};
				}

				replacements[CURRENT_LEVEL][start_pos] = {
					'replace': match[0],
					'with': replacement
				};

				replacement_positions[CURRENT_LEVEL][start_pos] = end_pos;

				processNext();
			},

			group_keys = keys(pattern['matches']),

			processGroup = function(i, group_keys, callback) {
				if (i >= group_keys.length) {
					return callback(replacement);
				}

				var processNextGroup = function() {
						processGroup(++i, group_keys, callback);
					},
					block = match[group_keys[i]];

				if (!block) {
					return processNextGroup();
				}

				var group = pattern['matches'][group_keys[i]],
					language = group['language'],

					process_group = group['name'] && group['matches'] ? group['matches'] : group,

					_replaceAndContinue = function(block, replace_block, match_name) {
						replacement = _replaceAtPosition(_indexOfGroup(match, group_keys[i]), block, match_name ? _wrapCodeInSpan(match_name, replace_block) : replace_block, replacement);
						processNextGroup();
					};

				if (language) {
					return _highlightBlockForLanguage(block, language, function(code) {
						_replaceAndContinue(block, code);
					});
				}

				if (typeof group === 'string') {
					return _replaceAndContinue(block, block, group);
				}

				_processCodeWithPatterns(block, process_group.length ? process_group : [process_group], function(code) {
					_replaceAndContinue(block, code, group['matches'] ? group['name'] : 0);
				});
			};

		processGroup(0, group_keys, onMatchSuccess);
	}

	function _bypassDefaultPatterns(language)
	{
		return bypass_defaults[language];
	}

	function _getPatternsForLanguage(language) {
		var patterns = language_patterns[language] || [],
			default_patterns = language_patterns[DEFAULT_LANGUAGE] || [];

		return _bypassDefaultPatterns(language) ? patterns : patterns.concat(default_patterns);
	}

	function _replaceAtPosition(position, replace, replace_with, code) {
		var sub_string = code.substr(position);
		return code.substr(0, position) + sub_string.replace(replace, replace_with);
	}
	function keys(object) {
		var locations = [],
			replacement,
			pos;

		for(var location in object) {
			if (object.hasOwnProperty(location)) {
				locations.push(location);
			}
		}
		return locations.sort(function(a, b) {
			return b - a;
		});
	}
	function _processCodeWithPatterns(code, patterns, callback)
	{
		++CURRENT_LEVEL;

		function _workOnPatterns(patterns, i)
		{
			if (i < patterns.length) {
				return _processPattern(patterns[i]['pattern'], patterns[i], code, function() {
					_workOnPatterns(patterns, ++i);
				});
			}

			_processReplacements(code, function(code) {
				delete replacements[CURRENT_LEVEL];
				delete replacement_positions[CURRENT_LEVEL];
				--CURRENT_LEVEL;
				callback(code);
			});
		}

		_workOnPatterns(patterns, 0);
	}

	function _processReplacements(code, onComplete) {
		function _processReplacement(code, positions, i, onComplete) {
			if (i < positions.length) {
				++replacement_counter;
				var pos = positions[i],
					replacement = replacements[CURRENT_LEVEL][pos];
				code = _replaceAtPosition(pos, replacement['replace'], replacement['with'], code);

				var next = function() {
					_processReplacement(code, positions, ++i, onComplete);
				};

				return replacement_counter % 250 > 0 ? next() : setTimeout(next, 0);
			}

			onComplete(code);
		}

		var string_positions = keys(replacements[CURRENT_LEVEL]);
		_processReplacement(code, string_positions, 0, onComplete);
	}

	function _highlightBlockForLanguage(code, language, onComplete) {
		var patterns = _getPatternsForLanguage(language);
		_processCodeWithPatterns(_htmlEntities(code), patterns, onComplete);
	}

	function _highlightCodeBlock(code_blocks, i, onHighlight) {
		if (i < code_blocks.length) {
			var block = code_blocks[i],
				language = block.data('language');

			if (!block.hasClass('rainbow') && language) {
				language = language.toLowerCase();

				block.addClass('rainbow');

				return _highlightBlockForLanguage(block.text(), language, function(code) {
					replacements = {};
					replacement_positions = {};

					onHighlight && onHighlight(code, block, language);

					setTimeout(function() {
						_highlightCodeBlock(code_blocks, ++i, onHighlight);
					}, 0);
				});
			}

			return _highlightCodeBlock(code_blocks, ++i, onHighlight);
		}
	}

	function onHighlight(code, block, lang) {
		var hl = block.data('highlight'),
			line_start = block.data('start');

		if (code.match(/[\n\r]/) || line_start) {
			code =
				code
				.replace(/\n?\r/g, '\n')
				.replace(/\t/g, '    ')
				.split('\n');

			line_start = line_start ? Math.abs(parseInt(line_start, 10) - 1) : 0;

			block = $('<tr/>').appendTo($('<table/>').appendTo(block.empty()));
			var l = [{ addClass : $.noop }], // l[0].addClass()
				$line = $('<td class="syntax-line" />').appendTo(block),
				$code = $('<td class="syntax-code" />').appendTo(block);

			var tmp;

			$.each(code, function (i, line) {
				if (line === '')
					line = '<br />';

				l.push($('<div/>').html(line).appendTo($code));
				$line.append($('<div class="syntax-line-num" />').text(line_start + i + 1));
			});

			hl && $.each((hl + '').split(','), function (i, line) {
				if(line.indexOf('-') >= 0){
					var range = line.split('-'),
						start = parseInt(range[0], 10) - line_start,
						end = parseInt(range[1], 10) - line_start;

					for (; start <= end; start++) {
						l[start].addClass('syntax-highlight');
					}
				} else {
					line = parseInt(line, 10) - line_start;
					l[line].addClass('syntax-highlight');
				}
			});
		} else {
			block.html(code);
		}
	}

	window.Rainbow = {
		extend: function(language, patterns, bypass) {
			if (arguments.length == 1) {
				patterns = language;
				language = DEFAULT_LANGUAGE;
			}

			bypass_defaults[language] = bypass;
			language_patterns[language] = patterns.concat(language_patterns[language] || []);
		},
		color: function () {
			var blocks = [];

			$('code.syntax').each(function () {
				blocks.push($(this));
			});

			_highlightCodeBlock(blocks, 0, onHighlight);
		}
	};

})(jQuery);
