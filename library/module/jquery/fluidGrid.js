/*!
 * Fluid Grid
 *
 * @author ykiwng
 * @link http://github.com/creasty/fluidgrid
 *
 * @param {Object} settings
 * @param {Number} .columns - maximum of column number
 * @param {Number} .gutter - space between columns
 * @param {Number} .minWidth - minimum of a column width
 */

!function ($, window) {
	var $head = $('head');

	$head.append('<style>.grid-clear{ display: block; clear: both; padding: 0; }</style>');

	$.fn.fluidGrid = function (settings) {
		settings = $.extend({
			columns: 100,
			gutter: 10,
			minWidth: 100,
			exclude: null
		}, settings);

		return this.each(function () {
			var $parent = $(this).addClass('grid-container'),
				$boxes = $parent.children().not(settings.exclude).addClass('grid-item'),
				count = $boxes.length,
				clear = '<' + $boxes.eq(0)[0].tagName + ' class="grid-clear" />';

			var now;

			var layout = function () {
				var col = settings.columns,
					gutter = settings.gutter,
					maxWidth = $parent.width('100%').width() | 0,
					width;

				// bug fix for Firefox and Chrome
				$parent.width(maxWidth);

				col = Math.max(1, Math.min(col, (maxWidth + gutter) / (settings.minWidth + gutter) | 0));
				width = (maxWidth + gutter) / col - gutter;

				if (now == col) {
					$boxes.width(width);
					return;
				}

				// update & reset
				now = col;
				$parent.find('.grid-clear').remove();

				var row;

				$boxes.each(function (i) {
					++i;

					var $this = $(this).width(width),
						isLast = (i % col === 0 || i === count);

					$this.css({
						'marginRight': (isLast ? 0 : gutter),
						'marginBottom': gutter
					});

					isLast && $(clear).insertAfter($this);
				});
			};

			layout();
			$(window).resize(layout);
		});
	};
}(jQuery, window);