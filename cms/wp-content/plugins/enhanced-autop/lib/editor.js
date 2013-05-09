
/*=== New Algorithm
==============================================================================================*/
(function(){
	var $TK = 'address|article|aside|blockquote|caption|col|colgroup|details|div|dl|embed|fieldset|figcaption|figure|footer|form|header|hgroup|input|legend|map|math|menu|nav|object|ol|object|param|pre|section|select|style|summary|table|tbody|tfoot|thead|tr|ul|wp_protect|wp_noautop';
	var $TD = 'a|audio|canvas|dd|del|dt|ins|label|li|map|noscript|script|style|td|th|video';
	var $TA = $TK + '|area|br|button|dd|dt|h[1-6]|hr|label|li|option|p|td|textarea|th|wp_br|wp_p';
	
	var $wp_protect = [];
	var $wp_protect_counter = 0;
	
	/**
	 * TinyMCE mode
	 */
	function autop($s){
		if(trim($s) === '')
			return '';
		
		$s = newlines($s);
		
		$s = protect($s, '<(pre|code|wp_noindent|wp_noautop)[^>]*>[\\s\\S]*?</\\1>', 0);
		$s = protect($s, '<(script|style)[^>]*>[\\s\\S]*?</\\1>', 1);
		$s = protect($s, '<\\?php[\\s\\S]*?\\?>', 1);
		$s = protect($s, '<!--[\\s\\S]*?-->', 2);
		
		$s = wrap($s);
		
		//$s = indent($s, -2);
		//$s = preg_replace(['^\\s+', 'm'], '', $s);
		
		$s = protect_done($s, 0);
		
		$s = preg_replace(['<((\\w+)[^>]*)>\\s*</\\2>'], '<$1></$2>', $s);
		return $s;
	}
	
	/**
	 * HTML mode
	 */
	function restore($s){
		if(trim($s) === '')
			return '';
		
		$s = newlines($s);
		
		var trimmed;
		$s = preg_replace(['<((pre|code)[^>]*)>([\\s\\S]*?)</\\2>'], function(_0, _1, _2, _3){
			trimmed = trim(_3, '\\n');
			
			if(trimmed.indexOf('\n') >= 0)
				_3 = preg_replace(['^\\n*'], '\n', _3);
			else
				_3 = trimmed;
			
			return '<' + _1 + '>' + _3 + '</' + _2 + '>';
		}, $s);
		
		$s = protect($s, '<(pre|wp_noindent)[^>]*>[\\s\\S]*?</\\1>', 0);

		$s = preformatting($s);
		
		//$s = indent($s, -2);
		$s = preg_replace(['^\\s+', 'm'], '', $s);
		
		$s = protect_done($s, 1);

		$s = preg_replace(['(\\t+)(<(script|style)[^>]*>[\\s\\S]+?</\\3>)'], function(_0, _1, _2){
			_2 = preg_replace(['^', 'm'], _1, _2);
			return _2;
		}, $s);
		
		$s = protect_done($s, 2);
		
		$s = preg_replace(['<((\\w+)[^>]*)>\\s*</\\2>'], '<$1></$2>', $s);
		
		return $s;
	}

	/**
	 * Cross-platform newlines
	 */
	function newlines($s){
		$s = str_replace("\r\n", "\n", $s);
		$s = str_replace("\r" , "\n", $s);
		return $s;
	}

	/**
	 * Ensure that the block/transparent tags are in independent lines
	 */
	function preformatting($s){
		$s = preg_replace(['\\s*<(/?(?:' + $TK + ')(?:\\s[^>]*)?)>\\s*'], "\n\n<$1>\n\n", $s);
		$s = preg_replace(['(</(?:' + $TA + ')>)'], "$1\n\n", $s);
		$s = preg_replace(['(<p(?:\\s[^>]*)?>[\\s\\S]*?</p>)'], "\n\n$1\n\n", $s);
		$s = preg_replace(['<br\\s*/?>\\s*<br\\s*/?>'], "\n\n", $s);
		
		$s = preg_replace(['\\n\\s*\\n'], '<wp_dnl />', $s);
		$s = str_replace('\n', '<wp_snl />', $s);
		$s = str_replace('<wp_dnl />', "\n\n", $s);
		
		$s = preg_replace(['<((' + $TD + ')(?:\\s[^>]*)?)>([^\\n]*?)</\\2>'], function(_0, _1, _2, _3){
			if(preg_match(['^\\s*<wp_snl />'], _3) && preg_match(['<wp_snl />\\s*$'], _3))
				return _0;
			
			_3 = preg_replace(['<(/?)((' + $TD + ')(?:\\s[^>]*)?)>'], '<$1wp_ni_$2>', _3);
			return "<wp_ni_" + _1 + ">" + _3 + "</wp_ni_" + _2 + ">";
		}, $s);
		
		$s = preg_replace(['<(/?(' + $TD + ')(?:\\s[^>]*)?)>'], "\n\n<$1>\n\n", $s);
		
		$s = preg_replace(['<(/?)wp_ni_([^>]+)>'], '<$1$2>', $s);
		$s = str_replace('<wp_snl />', "\n", $s);
		$s = preg_replace(["\\n\\n+"], "\n\n", $s); // take care of duplicates
		
		$s = preg_replace(['(\\s*<wp_protect id="wp-protect-\\d-\\d+"[^>]*>\\s*</wp_protect>)'], "\n\n$1\n\n", $s);

		return $s;
	}

	/**
	 * Make Paragaraphs
	 */
	function wrap($s){
		var $ss = $s.split(new RegExp('\\n\\s*\\n', 'g'));
		var $i = 0, $tinkle;
		
		$s = '';
		
		while(($tinkle = $ss[$i++]) != null){
			$s += '<wp_p>' + trim($tinkle, "\\n") + "</wp_p>\n";
		}
		
		$s = preg_replace(['<wp_p>\\s*</wp_p>\n*'], '', $s);
		$s = preg_replace(['<wp_p>\\s+'], '<wp_p>', $s);
		
		$s = preg_replace(['<wp_p>(</?(?:' + $TK + '|' + $TD + ')(?:\\s[^>]*)?>)</wp_p>'], '$1', $s);
		$s = preg_replace(['<wp_p>(</?(?:' + $TK + '|li)(?:\\s[^>]*)?>)'], '$1', $s);
		$s = preg_replace(['(</?(?:' + $TK + '|li)(?:\\s[^>]*)?>)\\s*</wp_p>'], '$1', $s);
		
		$s = preg_replace(['\\n(\\s*)(\\S)'], "\n$1<wp_br />$2", $s);
		$s = preg_replace(['<wp_br />(\\s*</?(?:' + $TA + ')(?:\\s[^>]*)?>)'], '$1', $s);
		$s = preg_replace(['<wp_br />(\\s*</?(?:' + $TD + ')(?:\\s[^>]*)?>)(</wp_p>)?$', 'm'], '$1', $s);
		
		$s = preg_replace(['<br\\s*/?>\\n(\\s*)<wp_br />(\\S)'], "$1$2", $s);

		$s = preg_replace(['<(/?)wp_(p|br)(\\s/)?>'], '<$1$2$3>', $s);
		
		return $s;
	}
	
	/**
	 * Smart indention
	 */
	function indent($s, $indent){
		var $lines = $s.split('\n');
		var $tab = {};
		var $i = 0, $tinkle, $m;
		
		$s = '';
		
		while(($tinkle = $lines[$i++]) != null){
			$tinkle = trim($tinkle);
			
			$m = preg_match(['^<(/?)(\\w+)[^>]*?(/?)>$'], $tinkle);
			
			if(!!$m){
				if(in_array($m[2], ['br', 'hr', 'img', 'input', 'wp_noautop'])) // do not change indention level
					$m[3] = true;
				else if($m[1] && !$m[3])
					$indent--;
			}
			
			if($indent > 0)
				$s += $tab[$indent] || ($tab[$indent] = str_repeat("\t", $indent));
			
			$s += $tinkle + "\n";
			
			if(!!$m && !$m[1] && !$m[3])
				$indent++;
		}
		
		return $s;
	}
	
	/*	Protect
	-----------------------------------------------*/
	function protect($content, $pattern, $group){
		if(!$wp_protect[$group])
			$wp_protect[$group] = [];
		
		$content = preg_replace([$pattern, 'i'], function(_0){
			$id = $wp_protect_counter++;
			$wp_protect[$group][$id] = _0;
			return '<wp_protect id="wp-protect-' + $group + '-' + $id + '" contenteditable="false"></wp_protect>';
		}, $content);
		
		return $content;
	}
	function protect_done($content, $group){
		$content = preg_replace(
			['<wp_protect id="wp-protect-' + $group + '-(\\d+)"[^>]*>\\s*</wp_protect>'],
			function(_0, _1){
				return $wp_protect[$group][parseInt(_1, 10)] || _0;
			},
			$content
		);
		
		//$wp_protect[$group] = [];
		return $content;
	}
	
	/*	PHP Like Function
	-----------------------------------------------*/
	function trim(a, b){
		b = b || '\\s';
		a = preg_replace(['^[' + b + ']*(.+?)[' + b + ']*$'], '$1', a);
		return a;
	}
	function str_replace(a, b, str){
		return str.split(a).join(b);
	}
	function preg_replace(a, b, c){
		return c.replace(new RegExp(a[0], (a[1] || '') + 'g'), b);
	}
	function preg_match(a, b){
		return new RegExp(a[0], (a[1] || '') + 'g').exec(b);
	}
	function in_array(a, b){
		return b.indexOf(a) >= 0;
	}
	function str_repeat(a, b){
		return new Array(b + 1).join(a);
	}
	
	/*	Simple HTML Escape (escapes only '<', '>', '&')
	-----------------------------------------------*/
	function esc_html(html){
		html = str_replace('<', '&lt;', html);
		html = str_replace('>', '&gt;', html);
		html = str_replace(['&(?![#\\w]+;)'], '&amp;', html);
		return html;
	}
	function unesc_html(html){
		html = str_replace('&lt;', '<', html);
		html = str_replace('&gt;', '>', html);
		html = str_replace('&amp;', '&', html);
		return html;
	}
	
	/*	Expose to the global
	-----------------------------------------------*/
	window.enhanced_autop_autop = autop;
	window.enhanced_autop_restore = restore;
})();


/*=== Apply
==============================================================================================*/
if(typeof switchEditors == 'undefined')
	switchEditors = {};

switchEditors._wp_Autop = enhanced_autop_autop;
switchEditors._wp_Nop = enhanced_autop_restore;

switchEditors.setupcontent_callback = function(editor_id, body){
	var c = document.getElementById(editor_id),
		formatted = switchEditors.wpautop(c.value);
	
	c.value = formatted;
	body.innerHTML = formatted;
};

/*	IE Fix
-----------------------------------------------*/
document.createElement('wp_protect');
document.createElement('wp_noautop');
document.createElement('wp_noindent');
