/**
 * dyn-slider
 * iView Wrapper
 */

(function ($) {
	var $head = $('head');
	var uid_counter = 0;

	var slider = function ($this, config) {
		var defaults,
			type = config.type || $this.data('slide');

		if ('bullets' == type)
			defaults = {
				directionNav: true,
				controlNav: true,
				controlNavNextPrev: false,
				controlNavTooltip: true
			};

		if ('thumbs' == type)
			defaults = {
				directionNav: true,
				controlNav: true,
				controlNavNextPrev: false,
				controlNavThumbs: true
			};

		var _config = $.extend(defaults, config);

		if ($this.data('duration'))
			_config.pauseTime = $this.data('duration');

		if ($this.data('auto'))
			_config.autoAdvance = $this.data('auto');

		var selector = $this.selector,
			width = _config.width || $this.data('width'),
			height = _config.height || $this.data('height');

		if (selector.indexOf('#') < 0) {
			var uid = $this.attr('id') || ('iview-' + uid_counter++);
			$this.attr('id', uid);
			selector = '#' + uid;
		}

		$head.append('<style>' + selector + ' .iviewSlider { width: ' + width + '; height: ' + height + '; }</style>');

		$this.addClass('iview-' + type).removeClass('hide');
		$this.iView(_config);
	};

	$.fn.slider = function (config) {
		config = config || {};

		return this.each(function () {
			slider($(this), config);
		});
	};

})(jQuery);
