/**
 * Social counter
 *
 * @author ykiwng
 */

(function ($, window) {
	var networks = {
		twitter: {
			api: 'http://urls.api.twitter.com/1/urls/count.json?url={url}&callback=?',
			url: 'https://twitter.com/intent/tweet?text={title}&url={url}&via={twitter}',
			count: function (info, $counter, $btn) {
				$.ajax({
					dataType: 'json',
					url: bind(this.api, info),
					success: function (obj) {
						successed(obj.count, $btn, $counter);
					},
					error: function () {
						failed($btn, $counter);
					}
				});
			},
			click: function (info) {
				window.open(
					bind(this.url, info),
					'',
					'toolbar=0, status=0, width=650, height=360'
				);
			}
		},
		facebook: {
			api: 'http://graph.facebook.com/{url}', // https://api.facebook.com/method/fql.query?query=select total_count, share_count from link_stat where url="{url}"&format=json
			url: 'http://www.facebook.com/sharer.php?u={url}', // http://www.facebook.com/plugins/like.php?href={url}
			count: function (info, $counter, $btn) {
				$.ajax({
					dataType: 'json',
					url: bind(this.api, info),
					success: function (obj) {
						successed(obj.shares, $btn, $counter);
					},
					error: function () {
						failed($btn, $counter);
					}
				});
			},
			click: function (info) {
				window.open(
					bind(this.url, info),
					'',
					'toolbar=0, status=0, width=900, height=500'
				);
			}
		},
		gplus: {
			api: '?network=gplus&id={url}',
			url: 'https://plusone.google.com/_/+1/confirm?hl={lang}&url={url}',
			count: function (info, $counter, $btn) {
				if (info.script) {
					$.ajax({
						dataType: 'json',
						url: bind(info.script + this.api, info),
						success: function (txt) {
							successed(txt, $btn, $counter);
						},
						error: function () {
							failed($btn, $counter);
						}
					});
				} else {
					failed($btn, $counter);
				}
			},
			click: function (info) {
				window.open(
					bind(this.url, info),
					'',
					'toolbar=0, status=0, width=900, height=500'
				);
			}
		},
		hatena: {
			api: 'http://api.b.st-hatena.com/entry.count?url={url}&callback=?',
			url: 'http://b.hatena.ne.jp/entry/{url}',
			count: function (info, $counter, $btn) {
				$.ajax({
					dataType: 'json',
					url: bind(this.api, info),
					success: function (txt) {
						successed(txt, $btn, $counter);
					},
					error: function () {
						failed($btn, $counter);
					}
				});
			},
			click: function (info) {
				window.open(
					bind(this.url, info),
					'',
					''
				);
			}
		},
		evernote: {
			init: function () {
				if(window.Evernote)
					return;

				$('<script src="http://static.evernote.com/noteit.js" async="async" />').insertBefore($('script:first-child'));
			},
			click: function (info) {
				window.Evernote && Evernote.doClip({
					providerName: info.name,
					url: info.url,
					title: info.title,
					contentId: 'main'
				});
			}
		},
		feedburner: {
			api: 'http://feedburner.google.com/api/awareness/1.0/GetFeedData?uri={feedburner}',
			count: function (info, $counter, $btn) {
				$.ajax({
					dataType: 'xml',
					url: bind(this.api, info),
					success: function (xml) {
						var txt;
						$(xml).find('entry').each(function () {
							txt = $(this).attr('circulation');
						});
						successed(txt, $btn, $counter);
					},
					error: function () {
						failed($btn, $counter);
					}
				});
			}
		}
	};

	function bind(tpl, hash) {
		return tpl.replace(/\{(\w+)\}/g, function (_0, key) {
			return hash[key] || '';
		});
	}

	function successed(num, $btn, $counter) {
		$counter.text(num || '0');
		$btn.show();
	}
	function failed($btn, $counter) {
		$btn.removeClass('has-counter');
		$counter.hide();
	}

	/*	Defaults from <meta />
	-----------------------------------------------*/
	var defaults;
	function init() {
		var $meta = $('meta');
		defaults = {
			url:
				$meta.filter('[property=og\\:url]').attr('content')
				|| $meta.filter('[name=canonical]').attr('content')
				|| window.location.href,
			title:
				$meta.filter('[property=og\\:title]').attr('content')
				|| $('title').text()
				|| '[NO TITLE]',
			site: $meta.filter('[property=og\\:site_name]').attr('content') || '',
			lang: $('html').attr('lang') || 'ja',
			twitter: ($meta.filter('[name=twitter\\:site]').attr('content') || '').replace(/^\@/, '')
		};
	}

	$.fn.socialShare = function (settings) {
		var $this = $(this);

		if (!settings.buttons)
			return $this;

		defaults || init();

		/*	Set
		-----------------------------------------------*/
		settings = $.extend(defaults, settings);

		if (!settings.feedburner)
			settings.buttons.feedburner = null;

		/*	Create Buttons
		-----------------------------------------------*/
		$.each(settings.buttons, function (network, label) {
			var ntwk = networks[network];

			if (!ntwk || !label)
				return;

			ntwk.init && ntwk.init();

			var $counter,
				$btn = $('<li class="' + network + '"><a href="#" class="link">' + label + '</a></li>').appendTo($this);

			if (ntwk.count) {
				$btn.addClass('has-counter').hide();
				$counter = $('<span class="counter">0</span>').appendTo($btn);
				ntwk.count(settings, $counter, $btn);
			}

			ntwk.click && $btn.children('.link').off('click').click(function (e) {
				e.preventDefault();
				$counter && $counter.text(parseInt($counter.text()) + 1); // simulate click
				ntwk.click(settings);
			});
		});

		return $this;
	};

})(jQuery, window);
