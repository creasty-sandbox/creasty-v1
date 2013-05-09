
!function ($) {
	$.fn.carousel = function (settings) {
		return this.each(function () {
			var $this, $prev, $next, $pagenation;

			$this = $(this);

			$this.addClass('carousel-items').wrap('<div class="carousel-wrapper" />');

			$prev = $('<a class="carousel-prev" href="#" />').insertAfter($this);
			$next = $('<a class="carousel-next" href="#" />').insertAfter($this);
			$pager = $('<div class="carousel-pager" />').insertAfter($this);

			settings = $.extend({
				prev: $prev,
				next: $next,
				pagination: {
					container: $pager,
					anchorBuilder: function(nr, item) {
						return '<span>&#x25cf;</span>';
					}
				},
				auto: false,
				responsive: true,
				width: '100%',
				swipe: {
					onTouch: true
				},
				items: 1
			}, settings || {});

			$this.carouFredSel(settings);

		});
	};
}(jQuery);
