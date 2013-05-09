/*!
 * Creasty JS
 *
 * @author ykiwng @ www.creasty.com
 */


/*=== Creasty Namespace
==============================================================================================*/
var creasty = {
	create: function (ns, set) {
		if (!ns || this._ns[ns])
			return;

		var parent = window,
			pp,
			name,
			spaces = ns.split('.'),
			i = 0;

		while (name = spaces[i++]) {
			if (parent[name] == null)
				parent[name] = {};

			pp = parent;
			parent = parent[name];

			this._ns[spaces.slice(0, i).join('.')] = true;
		}

		if (set)
			return (pp[spaces[i - 2]] = set);
	}
};


/*=== Define Packages
==============================================================================================*/
require.setPath('/library/', {
	'c': 'creasty/',
	'm': 'module/'
});

require.cache(false);

require.define({
	carousel: [
		'~m/carouFredSel/theme.css',
		'~m/carouFredSel/helper-plugins/jquery.touchSwipe.min.js',
		'~m/carouFredSel/jquery.carouFredSel-6.1.0-packed.js',
		'~m/carouFredSel/creasty-helper.js',
		function (silent) {
			silent || $(function () {
				$('.dyn-carousel').carousel();
			});
		}
	],
	collapse: [
		'~cm/Collapse/collapse.css',
		'~cm/Collapse/collapse.js'
	],
	data: [
		'~m/Data/data.css',
		'~m/Data/data.js'
	],
	fancybox: [
		'~m/Fancybox/jquery.fancybox.css',
		'~m/Fancybox/helpers/jquery.fancybox-buttons.css',
		'~m/Fancybox/helpers/jquery.fancybox-thumbs.css',
		'~m/Fancybox/jquery.fancybox.pack.js',
		'~m/Fancybox/helpers/jquery.fancybox-buttons.js',
		'~m/Fancybox/helpers/jquery.fancybox-thumbs.js',
		'~m/Fancybox/fancybox.js'
	],
	flipper: [
		'~m/Flipper/flipper.css',
		'~m/Flipper/flipper.js'
	],
	form: [
		'~m/Form/form.css'
		// '~m/Form/jquery.form.min.js'
	],
	notification: [
		'~m/Notification/style.css',
		'~m/Notification/notification.js'
	],
	pageslide: [
		'~m/Pageslide/jquery.pageslide.css',
		'~m/Pageslide/jquery.pageslide.min.js'
	],
	slider: [
		'~m/iView/skin/iview.css',
		'~m/iView/skin/skin.css',
		'~m/iView/iview.pack.js',
		'~m/iView/raphael-min.js',
		'~m/iView/helper.min.js',
		function (silent) {
			silent || $(function () {
				$('.dyn-slider').slider();
			});
		}
	],
	syntax: [
		'~m/Syntax/rainbow.css',
		'~m/Syntax/rainbow.min.js',
		'~m/Syntax/language/generic.js',
		'~m/Syntax/language/php.js',
		//'~m/Syntax/language/ruby.js',
		'~m/Syntax/language/javascript.js',
		//'~m/Syntax/language/coffeescript.js',
		'~m/Syntax/language/css.js',
		'~m/Syntax/language/html.js',
		function (silent) {
			silent || $(function () {
				Rainbow.color();
			});
		}
	],
	tab: [
		'~m/Tabnav/tabnav.css',
		'~m/Tabnav/tabnav.js'
	],
	tooltip: [
		'~m/PowerTip/jquery.powertip.css',
		'~m/PowerTip/jquery.powertip.js'
	],
	validator: [
		'~m/Form/jquery.form.min.js',
		'~m/FormValidator/FormValidator.min.js',
		function () {
			$.FormValidator.defaults = {
				groupSelector: '.col-ab-a',
				html: {
					message:      '<p class="help-block"></p>',
					notification: '<p class="alert"></p>'
				},
				className: {
					vaild:   'hide',
					invalid: 'show',
					success: 'alert-success',
					fail:    'alert-error'
				}
			};
		}
	]
});


/*=== On Ready
==============================================================================================*/
$(function () {
	/*	Smooth Scrolling
	-----------------------------------------------*/
	$('a[href^=#]').click(function (e) {
		e.preventDefault();

		var $this = $(this),
			id = $this.attr('href'),
			$target = $(id); // html5

		if ($target.length == 0)
			return;

		$this.removeClass('activeAnchor');
		$target.addClass('activeAnchor');

		$('html, body').animate({
			scrollTop: $target.offset().top
		}, {
			duration: 600,
			easing: 'easeInCubic',
			complete: function () {
				window.location.hash = id;
			}
		});
	});

	/*	Link Block
	-----------------------------------------------*/
	$('.link-block').each(function () {
		var $this = $(this),
			link = $this.find('a').attr('href');

		link && $this.css('cursor', 'pointer').bind('click tap', function () {
			window.location.href = link;
		});
	});

	/*	Fade Links
	-----------------------------------------------*/
	function fadeLinks(selector, duration) {
		var $document = $(document);

		$(selector).each(function () {
			var $this = $(this),
				$parent = $this.parent(),
				$hover = $this.clone();

			$parent
			.bind('mouseenter touchstart', function () {
				$this.stop().animate({ opacity: 0 }, duration);
				$hover.stop().animate({ opacity: 1 }, duration);
			})
			.bind('mouseleave touchmove', function () {
				$this.stop().animate({ opacity: 1 }, duration);
				$hover.stop().animate({ opacity: 0 }, duration);
			});

			$this.after($hover.html('').addClass('hover').css('opacity', 0));
		});
	}

	//fadeLinks('#creasty-logo a, #powered-by a', 500);

});

/*	Global Header
-----------------------------------------------*/
require('pageslide', 'module.jquery.here', '!ready').done(function () {
	$('#gnav-site-menu > li > a').here(true);
	$('#gnav-site-menu').before('<a id="gnav-toggle" href="#gnav-site-menu">l</a>');
	$('#gnav-toggle').addClass('show').pageslide({ direction: 'left' });
});

/*	Tootip
-----------------------------------------------*/
require('tooltip', '!ready').done(function () {
	$('#powered-by li, .tooltip, abbr[title]').powerTip({
		placement: 'n',
		smartPlacement: true
	});
	$('#globalheader > nav > #gnav-site-menu > li > a').powerTip({
		placement: 's',
		smartPlacement: true,
		offset: 0
	});
});


/*=== Old Bitch
==============================================================================================*/
(function (document) {
	var tags = 'abbr article aside audio bb canvas datagrid datalist details dialog eventsource figure footer header hgroup mark menu meter nav output progress section time video';

	$.each(tags.split(' '), function (i, tag) {
		document.createElement(tag);
	});
})(document);

if (Device.ie && Device.version < 8 || Device.firefox && Device.version < 5) {
	$(function () {
		$('body').prepend('<div id="old-browser"><strong>お使いのブラウザはバージョンが古いため</strong>、サイトを快適にご利用いただけないかもしれません。<a href="http://browsehappy.com/">最新のブラウザにアップグレード</a> するか、<a href="http://www.google.com/chromeframe/?redirect=true">Google Chrome Frame をインストール</a> してください。</div>');
	});
}


/*=== Google Analytics
==============================================================================================*/
var _gaq = _gaq || [];
_gaq.push([ '_setAccount', 'UA-4498942-3' ]);
_gaq.push([ '_setDomainName', 'creasty.com' ]);
_gaq.push([ '_trackPageview' ]);
(function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();

