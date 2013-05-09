
var switchEditors = {

	switchto: function(el) {
		var aid = el.id, l = aid.length, id = aid.substr(0, l - 5), mode = aid.substr(l - 4);

		this.go(id, mode);
	},

	go: function(id, mode) { // mode can be 'html', 'tmce', or 'toggle'
		id = id || 'content';
		mode = mode || 'toggle';

		var t = this, ed = tinyMCE.get(id), wrap_id, txtarea_el, dom = tinymce.DOM;

		wrap_id = 'wp-'+id+'-wrap';
		txtarea_el = dom.get(id);

		if ( 'toggle' == mode ) {
			if ( ed && !ed.isHidden() )
				mode = 'html';
			else
				mode = 'tmce';
		}

		if ( 'tmce' == mode || 'tinymce' == mode ) {
			if ( ed && ! ed.isHidden() )
				return false;

			if ( typeof(QTags) != 'undefined' )
				QTags.closeAllTags(id);

			if ( tinyMCEPreInit.mceInit[id] && tinyMCEPreInit.mceInit[id].wpautop )
				txtarea_el.value = t.wpautop( txtarea_el.value );

			if ( ed ) {
				ed.show();
			} else {
				ed = new tinymce.Editor(id, tinyMCEPreInit.mceInit[id]);
				ed.render();
			}

			dom.removeClass(wrap_id, 'html-active');
			dom.addClass(wrap_id, 'tmce-active');
			setUserSetting('editor', 'tinymce');

		} else if ( 'html' == mode ) {

			if ( ed && ed.isHidden() )
				return false;

			if ( ed ) {
				txtarea_el.style.height = ed.getContentAreaContainer().offsetHeight + 20 + 'px';
				ed.hide();
			}

			dom.removeClass(wrap_id, 'tmce-active');
			dom.addClass(wrap_id, 'html-active');
			setUserSetting('editor', 'html');
		}
		return false;
	},

	_wp_Nop : function(content) {
				return content;
	},

	_wp_Autop : function(pee) {
			return pee;
	},

	pre_wpautop : function(content) {
		var t = this, o = { o: t, data: content, unfiltered: content },
			q = typeof(jQuery) != 'undefined';

		if ( q )
			jQuery('body').trigger('beforePreWpautop', [o]);
		o.data = t._wp_Nop(o.data);
		if ( q )
			jQuery('body').trigger('afterPreWpautop', [o]);

		return o.data;
	},

	wpautop : function(pee) {
		var t = this, o = { o: t, data: pee, unfiltered: pee },
			q = typeof(jQuery) != 'undefined';

		if ( q )
			jQuery('body').trigger('beforeWpautop', [o]);
		o.data = t._wp_Autop(o.data);
		if ( q )
			jQuery('body').trigger('afterWpautop', [o]);

		return o.data;
	}
}

