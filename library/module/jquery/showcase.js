/**
 * Showcase
 *
 * @author ykiwng, www.creasty.com
 */

(function ($, window) {
	var $htmlbody;
	var $panel, $panel_content, $arrow;

	var cache = {};

	var showcase = function (el, config) {
		this.el = el;
		this.config = config;
		this.init();

		var that = this;

		el.click(function (e) {
			e.preventDefault();
			that.open($(this), true);
		});

		if (config.open != null)
			$(window).load(function () {
				that.open(config.open, false);
			});
	};

	showcase.prototype = {
		init: function () {
			if ($panel)
				return;

			$panel = $(this.config.panel);
			$panel_content = $(this.config.content);
			$arrow = $(this.config.arrow);

			$panel.append($panel_content);
			$('body').append($panel, $arrow);

			$htmlbody = $('html, body');
		},
		_load: function (data) {
			$panel_content.removeClass('loading');
			$panel_content.append(data);
			this.config.callback($panel_content, !cache[this.url]);
			cache[this.url] = $panel_content.contents();
		},
		load: function () {
			var that = this;

			this.prev = this.parent;
			this.parent.addClass('open');
			this.parent.data('panel-state', 'open');
			this.parent.nextAll(this.config.valley).eq(0).append($panel);

			$panel_content.addClass('loading');

			if (cache[this.url])
				this._load(cache[this.url]);
			else
				$.ajax({
					url: this.url,
					data: this.config.ajax,
					success: function (data) {
						that._load(data);
					}
				});
		},
		unload: function () {
			$panel_content.contents().detach();
		},
		moveArrow: function () {
			var coor = this.parent.offset();

			this.coor = { top: coor.top, left: coor.left };

			coor.left += this.parent.width() / 2;
			coor.top += this.parent.height();

			if (this.isSwitch) {
				$arrow.offset({ top: coor.top }).animate({ left: coor.left });
			} else {
				coor.top -= 20;

				$arrow.show().offset(coor).hide().animate({
					opacity: 'show',
					top: '+=20'
				}, {
					duration: 600,
					easing: 'easeOutCubic'
				});
			}
		},
		openPanel: function () {
			if (this.isSwitch)
				return;

			$panel.animate({
				height: 'show',
				top: 20
			}, {
				duration: 600,
				easing: 'easeOutCubic'
			});
		},
		switchPanel: function () {
			if (!this.isSwitch)
				return;

			this.prev.removeClass('open');
			this.prev.data('panel-state', 'close');
			this.unload();
		},
		scroll: function (scroll) {
			if (!scroll)
				return;

			$htmlbody.animate({
				scrollTop : this.coor.top
			}, {
				duration : 600,
				easing : 'easeInCubic'
			});
		},
		open: function ($this, scroll) {
			if ($.isNumeric($this))
				$this = this.el.eq($this);

			this.isSwitch = !!this.prev;
			this.parent = $this.parent();

			if (this.parent.data('panel-state') == 'open')
				return this.close();

			this.switchPanel();

			this.url = $this.attr('href');
			this.load();

			this.moveArrow();
			this.openPanel();
			this.scroll(scroll);
		},
		close: function () {
			this.parent.removeClass('open');
			this.parent.data('panel-state', 'close');

			$arrow.animate({
				opacity: 'hide',
				top: '-=20'
			}, {
				duration: 600,
				easing: 'easeOutCubic'
			});

			$panel.animate({
				height: 'hide',
				top: 0
			}, {
				duration: 600,
				easing: 'easeOutCubic',
				complete: this.unload
			});

			this.prev = null;
		}
	};

	$.fn.showcase = function (config) {
		config = $.extend({
			open:     null,
			callback: $.nope,
			ajax:     { '_ajax': 1 },
			arrow:    '<div id="showcase-arrow" />',
			panel:    '<div id="showcase-panel" />',
			content:  '<div id="showcase-panel-content" />',
			valley:   '.showcase-valley'
		}, config);

		new showcase($(this), config);

		return this;
	};

})(jQuery, window);
