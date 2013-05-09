<?php
/**
 * Plugin Name: Enhanced Autop
 * Plugin URI: http://www.github.com/ykiwng/enhanced_autop
 * Description: Revolutional improvement of autop
 * Author: Yuki Iwanaga
 * Author URI: http://www.creasty.com/
 * Version: 1.1
 * Requires at least: 3.3
 * Tested up to: 3.4.2
 * Stable tag: 1.0
 */

if (!defined('ABSPATH'))
	die('You are not allowed to call this page directly.');

// Bug
// http://wordpress.org/support/topic/trouble-using-get_current_user_id
require_once(ABSPATH . 'wp-includes/pluggable.php');


define('TAGSET_TK', 'address|article|aside|blockquote|caption|col|colgroup|details|div|dl|embed|fieldset|figcaption|figure|footer|form|header|hgroup|input|legend|map|math|menu|nav|object|ol|object|param|pre|section|select|style|summary|table|tbody|tfoot|thead|tr|ul|wp_preserve|wp_noautop');
define('TAGSET_TE', 'area|br|button|dd|dt|h[1-6]|hr|label|li|option|p|td|textarea|th|wp_br|wp_p');
define('TAGSET_TD', 'a|audio|canvas|dd|del|dt|ins|label|li|map|noscript|script|style|td|th|video');
define('TAGSET_TA', TAGSET_TD . '|' . TAGSET_TE);
define('TAGSET_TI', 'a');


class CodeCleaner {
	/**
	 * @constructor
	 */
	public function __construct() {
		$this->preserve = new PreserveTags();
	}

	/**
	 * @method regulate
	 * do NOT wrap with p tag
	 *
	 * 1. protect 'pre' and 'wp_noindent'
	 * 2. protect 'script' and 'style'
	 * 3. remove comments
	 * 4. wptexturize()
	 * 5. cross-platform newlines
	 * 6. add new lines around `tag_blocks`
	 * 7. detect and blocknize `tag_depends`
	 * 8. indent
	 * 9. marge duplicated lines and trim
	 */
	public function regulate($pee, $indent = false) {
		if (trim($pee) === '')
			return '';

		$this->preserve->reset();
		$pee = $this->preserve->apply($pee, 'pre|wp_noindent', 0);
		$pee = $this->preserve->apply($pee, 'script|style', 1);

		$pee = preg_replace('/<!--(.*?)-->/s', '', $pee); // remove comments

		$pee = wptexturize($pee);

		$pee = str_replace(array("\r\n", "\r"), "\n", $pee); // cross-platform newlines

		$pee = preg_replace('!\s*<(/?(?:' . TAGSET_TK . ')(?:\s[^>]*)?)>\s*!', "\n\n<$1>\n\n", $pee);
		$pee = preg_replace('!(</(?:' . TAGSET_TA . ')>)!', "$1\n\n", $pee);
		$pee = preg_replace('|(<p(?:\s[^>]*)?>.*?</p>)|s', "\n\n$1\n\n", $pee);
		$pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);

		$pee = preg_replace('!\n\s*\n!u', '<wp_dnl />', $pee);
		$pee = str_replace("\n", '<wp_snl />', $pee);

		$pee = preg_replace('!<((?:' . TAGSET_TA . ')(?:\s[^>]*)?)>\s*<wp_snl />!', '<$1>', $pee);
		$pee = preg_replace('!<wp_snl />\s*<(/(?:' . TAGSET_TA . ')(?:\s[^>]*)?)>!', '<$1>', $pee);

		$pee = str_replace('<wp_dnl />', "\n\n", $pee);
		$pee = preg_replace_callback('!<((' . TAGSET_TD . ')(?:\s[^>]*)?)>([^\n]*?)</\2>!u', function($m){
			if (preg_match('!^\s*<wp_snl />!', $m[3]) && preg_match('!<wp_snl />\s*$!', $m[3]))
				return $m[0];

			$m[3] = preg_replace('!<(/?)((' . TAGSET_TD . ')(?:\s[^>]*)?)>!', '<$1wp_ni_$2>', $m[3]);
			return "<wp_ni_{$m[1]}>{$m[3]}</wp_ni_{$m[2]}>";
		}, $pee);

		$pee = preg_replace('!<(/?(' . TAGSET_TD . ')(?:\s[^>]*)?)>!', "\n\n<$1>\n\n", $pee);
		$pee = preg_replace('|\s*<(/?wp_ni_(' . TAGSET_TI . ')(?:\s[^>]*)?)>\s*|', '<$1>', $pee);

		$pee = preg_replace('|<(/?)wp_ni_([^>]+)>|', '<$1$2>', $pee);

		$pee = preg_replace('!(<(?:' . TAGSET_TK . '|' . TAGSET_TE . ')>)!', "\n\n$1", $pee);
		$pee = preg_replace('!(</(?:' . TAGSET_TK . '|' . TAGSET_TE . ')>)!', "$1\n\n", $pee);

		$pee = str_replace('<wp_snl />', "\n", $pee);
		$pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates

		$pee = $this->indent($pee, $indent);

		$pee = preg_replace('|\n\s*\n|', "\n", "\n$pee\n"); // take care of duplicates

		$pee = trim($pee, "\n");

		return $pee;
	}

	public function format($pee, $indent = false) {
		$pee = $this->preserve->done($pee, 1);

		if (is_numeric($indent) && $indent >= 0)
			$pee = preg_replace_callback('!(\t+)(<(script|style)[^>]*>.+?</\3>)!s', function($m){
				preg_match_all('|^\t*|mu', $m[0], $tab);
				$min = min($tab[0]);
				if($m[1] = substr($m[1], 0, strlen($m[1]) - strlen($min)))
					return $min . preg_replace('|^|m', $m[1], $m[2]);
				return $m[0];
			}, $pee);

		$pee = $this->preserve->done($pee, 0);
		$this->preserve->reset();

		$pee = preg_replace('|<((\w+)[^>]*)>\s*</\2>|', '<$1></$2>', $pee);
		$pee = preg_replace('!\t*</?wp_noindent>\n*!', '', $pee);

		return $pee;
	}

	public function indent($content, $indent) {
		if (!is_numeric($indent) || $indent < 0)
			return $content;

		static $tab = array();
		$lines = explode("\n", $content);
		$content = '';

		foreach ($lines as $tinkle) {
			$tinkle = trim($tinkle);
			$block = preg_match('!^<(/?)(\w+)[^>]*?(/?)>$!', $tinkle, $m);

			if ($block) {
				if (in_array($m[2], array('br', 'hr', 'img', 'input'))) // do not change indention level
					$m[3] = true;
				elseif ($m[1] && !$m[3])
					$indent--;
			}

			if ($indent > 0)
				$content .=
					$tab[$indent]
					? $tab[$indent]
					: ($tab[$indent] = str_repeat("\t", $indent));

			$content .= $tinkle . "\n";

			if ($block && !$m[1] && !$m[3])
				$indent++;
		}

		return $content;
	}

	public function wrap($pee) {
		$pees = preg_split('/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY); // make paragraphs, including one at the end
		$pee = '';

		foreach ($pees as $tinkle) {
			$pee .= '<wp_p>' . trim($tinkle, "\n") . "</wp_p>\n";
		}

		$pee = preg_replace('|<wp_p>\s*</wp_p>\n*|', '', $pee);
		$pee = preg_replace('|<wp_p>(\s+)|', '$1<wp_p>', $pee);
		$pee = preg_replace('|<wp_p>(<li.+?)</wp_p>|', '$1', $pee); // problem with nested lists
		$pee = preg_replace('!<wp_p>(</?(?:' . TAGSET_TA . ')(?:\s[^>]*)?>)!', '$1', $pee);
		$pee = preg_replace('!(</?(?:' . TAGSET_TA . ')(?:\s[^>]*)?>)\s*</wp_p>!', '$1', $pee);

		$pee = preg_replace('|(?<!<br />)\n(\s*)(\S)|', "\n$1<wp_br />$2", $pee);
		$pee = preg_replace('!<wp_br />(\s*</?(?:' . TAGSET_TA . ')(?:\s[^>]*)?>)!', '$1', $pee);

		$pee = preg_replace('!<wp_p>(</?(?:' . TAGSET_TD . ')(?:\s[^>]*)?>)</wp_p>!', '$1', $pee);
		$pee = preg_replace('!(<(?:' . TAGSET_TD . ')(?:\s[^>]*)?>)</wp_p>!', '$1', $pee);
		$pee = preg_replace('!(<\w+[^>]*>)</wp_p>!', '$1', $pee);
		$pee = preg_replace('!<wp_p>(</\w+[^>]*>)!', '$1', $pee);
		$pee = preg_replace('!<wp_p>(<(?:' . TAGSET_TD . ')(?:\s[^>]*)?>)$!m', '$1', $pee);
		$pee = preg_replace('!<wp_br />(\s*</(?:' . TAGSET_TD . ')(?:\s[^>]*)?>)(</wp_p>)?$!m', '$1', $pee);
		$pee = preg_replace('!(<(\w+)[^>]*>\s*)<wp_p>(<(?:img)[^>]*>)</wp_p>(\s*</\2>)!', '$1$3$4', $pee);
		$pee = preg_replace_callback('!\t*<wp_noautop>\n*(.+?)\t*</wp_noautop>\n*!s', function($m){
			$m[1] = preg_replace('!<(/?)wp_(p|br)(\s/)?>!', '', $m[1]);
			return $m[1];
		}, $pee);

		$pee = preg_replace('!<(/?)wp_(p|br)(\s/)?>!', '<$1$2$3>', $pee);

		return $pee;
	}
}

class PreserveTags {
	private $contents;
	private $id;
	private $group;

	/**
	 * @constructor
	 */
	public function __construct() {
		$this->reset();
	}

	public function reset() {
		$this->contents = array();
		$this->id = 0;
		$this->group = 0;
	}

	private function add($m) {
		$id = $this->id++;
		$tmp = "<wp_preserve id-$id />";
		$this->contents[$this->group][$tmp] = $m[0];
		return $tmp;
	}

	public function apply($content, $tag, $group = 0) {
		$this->group = $group;
		$this->contents[$group] = array();
		return preg_replace_callback('!<(' . $tag . ')(?:\s[^>]*)?>.*?</\\1>!is', array(&$this, 'add'), $content);
	}

	public function done($content, $group = 0) {
		$tmps = &$this->contents[$group];

		if (empty($tmps))
			return $content;

		return str_replace(array_keys($tmps), array_values($tmps), $content);
	}
}

class EnhancedAutop {
	public $lib;

	/**
	 * @constructor
	 */
	public function __construct() {
		$this->lib = plugins_url('/lib/', __FILE__);
		$this->rich_editing = get_user_option('rich_editing', get_current_user_id()) == 'true';

		add_action('init', array(&$this, 'init'));

		if ($this->rich_editing) {
			add_filter('tiny_mce_before_init', array(&$this, 'tiny_mce_before_init'));
			add_action('before_wp_tiny_mce', array(&$this, 'before_wp_tiny_mce'), 11);
			add_action('media_buttons', array(&$this, 'remove_wp_richedit_pre'));
			add_action('media_buttons', array(&$this, 'add_new_richedit_pre'), 9);
		}
	}

	/**
	 * Hook 'init'
	 */
	public function init() {
		global $wp_filter;
		$filters = array('the_content', 'term_description', 'the_excerpt', 'comment_text');

		$cc = new CodeCleaner();

		foreach ($filters as $filter) {
			remove_filter($filter, 'shortcode_unautop');
			remove_filter($filter, 'wpautop');
			remove_filter($filter, 'wptexturize');

			add_filter($filter, array(&$cc, 'regulate'), 20, 2);
			add_filter($filter, array(&$cc, 'format'), 9999, 2);
		}

		/*	Shortcodes before `before_wpautop()`
		-----------------------------------------------*/
		remove_filter('the_content', 'do_shortcode', 11);
		add_filter('the_content', 'do_shortcode', -9999);
	}

	/**
	 * @method before_wp_tiny_mce
	 * replace editor.js with new algorithm
	 */
	public function before_wp_tiny_mce() {
		echo '<script src="', $this->lib, 'editor.js"></script>';
	}

	/**
	 * @method tiny_mce_before_init
	 * tinymce settings
	 */
	public function tiny_mce_before_init($settings) {
		$settings['remove_linebreaks'] = false;
		$settings['fix_list_elements'] = true;
		$settings['verify_css_classes'] = false;
		$settings['convert_newlines_to_brs'] = false;
		$settings['apply_source_formatting'] = false;
		$settings['force_br_newlines'] = false;
		$settings['forced_root_block'] = '';
		$settings['element_format'] = '';

		$settings['extended_valid_elements'] =
			($settings['extended_valid_elements'] ? $settings['extended_valid_elements'] . ',' : '')
			. 'wp_preserve[*],wp_noautop[*],wp_noindent[*]';

		$settings['setupcontent_callback'] = 'switchEditors.setupcontent_callback';

		return $settings;
	}

	/**
	 * @method remove_wp_richedit_pre
	 * remove original hook
	 */
	public function remove_wp_richedit_pre() {
		remove_filter('the_editor_content', 'wp_richedit_pre');
	}

	/**
	 * @method add_new_richedit_pre
	 * register new hook
	 */
	public function add_new_richedit_pre() {
		global $wp_filter;

		if (isset($wp_filter['the_editor_content'][10]['wp_richedit_pre']))
			add_filter('the_editor_content', array(&$this, 'new_richedit_pre'));
	}

	/**
	 * @method new_richedit_pre
	 * use new algorithm
	 */
	public function new_richedit_pre($text) {
		if (empty($text))
			return apply_filters('richedit_pre', '');

		$output = convert_chars($text);
		$output = htmlspecialchars($output, ENT_NOQUOTES);

		return apply_filters('richedit_pre', $output);
	}
}


class BeautifyCode {
	/*	Autop
	-----------------------------------------------*/
	public static function init() {
		new EnhancedAutop();
	}

	/*	Anywhere
	-----------------------------------------------*/
	public static function begin() {
		ob_start();
	}
	public static function end($indent = false, $wrap = false) {
		$render = ob_get_contents();
		ob_end_clean();

		$cc = new CodeCleaner();
		$render = $cc->regulate($render, $indent);

		if ($wrap)
			$render = $cc->wrap($render);

		$render = $cc->format($render, $indent);

		echo $render;
	}

	/*	Template tag: the_content()
	-----------------------------------------------*/
	public static function the_content($indent = false, $more_link_text = null, $stripteaser = false) {
		echo self::get_the_content($indent, $more_link_text, $stripteaser);
	}
	public static function get_the_content($indent = false, $more_link_text = null, $stripteaser = false) {
		$content = get_the_content($more_link_text, $stripteaser);
		$content = apply_filters('the_content', $content, $indent);
		$content = str_replace(']]>', ']]&gt;', $content);
		$content = rtrim($content);
		return $content;
	}
}

BeautifyCode::init();



