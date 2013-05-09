<?php

class CreastyTheme {
	public $path;
	public $files = array();

	/**
	 * @constructor
	 */
	public function __construct() {
		if (!$this->path)
			$this->path = TEMPLATEPATH . '/functions/';

		/*	Load
		-----------------------------------------------*/
		foreach ($this->files as $f) {
			require_once($this->path . $f);
		}

		if (is_admin())
			$this->load_admin_php();

		/*	Hooks
		-----------------------------------------------*/
		add_action('init', array(&$this, 'deregister_jquery'), 20);
		add_action('admin_bar_menu', array(&$this, 'remove_admin_bar_logo'), 999);

		remove_action('wp_head', 'wp_shortlink_wp_head', 10);
		remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
		remove_action('wp_head', 'wp_generator');
		remove_action('wp_head', 'rel_canonical');
		remove_action('wp_head', 'feed_links_extra', 3);

		add_action('get_header', array(&$this, 'wp_head_cleaner_start'), 9999);
		add_action('wp_head', array(&$this, 'wp_head_cleaner_end'), 9999);

		add_action('tpl_init', array(&$this, 'tpl_init'));
		add_action('get_header', array(&$this, 'tpl_init'));
		add_action('wp_head', array(&$this, 'common_head'), 0);

		add_filter('post_class', array(&$this, 'post_class'));
	}

	/**
	 * @method load_admin_php
	 * load program file for admin if avaliable
	 */
	public function load_admin_php($file = 'admin.php') {
		$file = $this->path . $file;

		if (file_exists($file))
			require_once($file);
	}

	/**
	 * @method tpl_init
	 * initialize template params
	 */
	public function tpl_init() {
		global $params, $post;

		$title = crst::title_array();

		$params = array(
			'template' =>
				get_template_info(),
			'canonical' =>
				canonical_url($post),
			'pagetitle' =>
				$title,
			'doctitle' =>
				$title['doc'],
			'description' =>
				is_singular()
				? crst::get_the_excerpt($post, 110, true, false)
				: null,
		);

		$params = apply_filters('tpl_param', $params);

		/*	Pagetitle
		-----------------------------------------------*/
		if ($params['pagetitle'])
			$params['pagetitle'] = crst::title_from_array($params['pagetitle']);
	}

	/**
	 * @method common_head
	 * output a base html head
	 */
	public function common_head() {
		global $post, $params;

		/*	Meta
		-----------------------------------------------*/
		if (get_tpl_param('description'))
			echo '<meta name="description" content="', esc_attr(get_tpl_param('description')), '" />';

		/*	OGP
		-----------------------------------------------*/
		if (get_option('facebook_id') && get_tpl_param('canonical')){
			$og_type = is_front_page() ? 'website' : (is_home() ? 'blog' : 'article');

			if (get_option('twitter'))
				echo '<meta name="twitter:card" content="summary" />',
					'<meta name="twitter:site" content="@', esc_attr(get_option('twitter')), '" />';

			echo '<meta property="og:site_name" content="', esc_attr(get_bloginfo('name')), '" />';
			echo '<meta property="og:title" content="', esc_attr(get_tpl_param('doctitle')), '" />';
			echo '<meta property="og:url" content="', get_tpl_param('canonical'), '" />';
			echo '<meta property="og:type" content="', $og_type, '" />';

			if (is_singular() && has_post_thumbnail()) {

				$thumb = get_post_thumbnail_id($post->ID);
				$img = wp_get_attachment_image_src($thumb, array(300, 300));

				if ($img) {
					if (!preg_match('|^(https?:)?//|', $img[0]))
						$img[0] = home_url($img[0]);

					echo '<meta property="og:image" content="', esc_url($img[0]), '" />';
				}
			}

			if ((is_front_page() || is_singular()) && get_tpl_param('description'))
				echo '<meta property="og:description" content="', esc_attr(get_tpl_param('description')), '" />';

			echo '<meta property="fb:app_id" content="', get_option('facebook_id'), '" />';
		}

		/*	Link
		-----------------------------------------------*/
		if (get_tpl_param('canonical'))
			echo '<link rel="canonical" href="', get_tpl_param('canonical'), '" />';

		if (is_single())
			echo '<link rel="shortlink" href="', home_url('?p=' . $post->ID), '" />';
	}

	/**
	 * @method deregister_jquery
	 */
	public function deregister_jquery() {
		if (is_admin())
			return;

		wp_deregister_script('jquery');
	}

	/**
	 * @method remove_admin_bar_logo
	 * hide WordPress logo on admin bar
	 */
	public function remove_admin_bar_logo($wp_admin_bar) {
		$wp_admin_bar->remove_menu('wp-logo');
	}

	/**
	 * @method wp_head_cleaner_start
	 * start capturing OB
	 */
	public function wp_head_cleaner_start() {
		ob_start();
	}

	/**
	 * @method wp_head_cleaner_end
	 * clean up tags in head
	 */
	public function wp_head_cleaner_end() {
		$render = ob_get_contents();
		ob_end_clean();

		$start = strpos($render, '<head');
		if ($start >= 0)
			$start = strpos($render, '>', $start);
		else
			$start = -1;

		echo mb_substr($render, 0, $start + 1), "\n";

		$render = mb_substr($render, $start + 1);
		$tags = array();

		$render = preg_replace('!\stype=("|\')text/(javascript|css)\1!', '', $render);

		preg_match_all('!<(meta|base)[^>]*?>!', $render, $meta);
		preg_match_all('!<link[^>]*?>!', $render, $link);
		preg_match_all('!\t*<style[^>]*?>.*?</style>!s', $render, $style);
		preg_match_all('!\t*<(noscript|script)[^>]*?>.*?</\1>!s', $render, $script);

		preg_match('!<title>.+?</title>!', $render, $title);

		sort($meta[0]);

		foreach ($script[0] as &$sc) {
			$sc = code_indent($sc, 0, true);
		}
		foreach ($style[0] as &$st) {
			$st = code_indent($st, 0, true);
		}

		$tags = array_merge($meta[0], $title, $link[0], $script[0], $style[0]);

		unset($meta, $title, $link, $script, $style);

		if (!empty($tags)) {
			$render = '';
			$render .= implode($tags, "\n");
			$render = code_indent($render, 1);
			echo $render . "\n";
		}

		unset($tags, $render);
	}

	/**
	 * @method use_relative_url
	 * make most template urls relative
	 */
	public function use_relative_url() {
		if (!defined('WP_USE_THEMES') || WP_USE_THEMES == false || is_feed() || is_admin())
			return;

		add_filter('attachment_link', 'relative_url', 1);
		add_filter('author_link', 'relative_url', 1);
		//add_filter('feed_link', 'relative_url', 1);
		add_filter('day_link', 'relative_url', 1);
		add_filter('month_link', 'relative_url', 1);
		add_filter('year_link', 'relative_url', 1);
		add_filter('term_link', 'relative_url', 1);
		add_filter('category_link', 'relative_url', 1);
		add_filter('page_link', 'relative_url', 1);
		add_filter('post_link', 'relative_url', 1);
		add_filter('the_permalink', 'relative_url');
		add_filter('get_pagenum_link', 'relative_url');
		add_filter('trackback_url', 'absolute_url');
	}

	/**
	 * @method remove_image_attrs
	 * remove title, width and height attributes from the output html
	 */
	public function remove_image_attrs() {
		add_filter('post_thumbnail_html', array(&$this, '_remove_image_attrs'), 10);
		add_filter('image_send_to_editor', array(&$this, '_remove_image_attrs'), 10);
	}
	public function _remove_image_attrs($html) {
		return preg_replace('/(width|height|title)="\d*"\s/', '', $html);
	}

	/**
	 * @method post_class
	 * add 'new' & 'updated' classes to `post_class`
	 */
	public function post_class($classes, $class = '', $post_id = null) {
		$post = get_post($post_id);

		if (crst::is_new($post))
			$classes[] = 'post-new';

		if (crst::is_updated($post))
			$classes[] = 'post-updated';

		return $classes;
	}

	/**
	 * @method lockout
	 * Lockout from non-login users
	 */
	public function lockout($page = 'redirect', $only_not_public = false) {
		global $pagenow;

		if (is_user_logged_in() || 'wp-login.php' == $pagenow || 'wp-register.php' == $pagenow )
			return;

		if ($only_not_public && '0' != get_option('blog_public'))
			return;

		nocache_headers();

		if ('redirect' == $page) {
			header("HTTP/1.1 302 Moved Temporarily");
			header('Location: ' . get_settings('siteurl') . '/wp-login.php?redirect_to=' .
			urlencode($_SERVER['REQUEST_URI']));
			header("Status: 302 Moved Temporarily");
		} elseif (is_readable($page)) {
			require($page);
		}

		exit();
	}
}

