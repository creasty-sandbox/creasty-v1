<?php

require_once(TEMPLATEPATH . '/functions/common/util.php');
require_once(TEMPLATEPATH . '/functions/common/CreastyTemplate.php');
require_once(TEMPLATEPATH . '/functions/common/CreastyFormatting.php');
require_once(TEMPLATEPATH . '/functions/common/CreastyTheme.php');


class my_function_php extends CreastyTheme {
	public $files = array(
		'module/breadcrumb.php',
		'module/pagenation.php',
		'module/search.php',
		'shortcodes.php',
	);

	public function __construct() {
		parent::__construct();

		$this->use_relative_url();
		$this->remove_image_attrs();

		/*	Theme support
		-----------------------------------------------*/
		add_theme_support('menus');
		add_theme_support('post-thumbnails');
		add_post_type_support('page', 'excerpt');

		/*	Image
		-----------------------------------------------*/
		set_post_thumbnail_size(300, 300, true);
		add_image_size('blog-image', 818, 300, true);
		add_image_size('hero-image', 1100, 0, true);

		/*	Template hooks
		-----------------------------------------------*/
		add_action('wp_head', array(&$this, 'head_tag'), 0);
		add_action('init', array(&$this, 'init'));

		add_action('admin_bar_menu', array(&$this, 'admin_bar_menu'), 999);

		add_filter('the_content', array(&$this, 'the_content'));
		add_filter('the_tags', array(&$this, 'the_tags'), 1);

		add_filter('bloginfo', array(&$this, 'bloginfo'));

		/*	Module hooks
		-----------------------------------------------*/
		add_filter('tpl_param', array(&$this, 'tpl_param'), 11);
		//add_filter('Pagenation::entriy_html', array(&$this, 'pagenation_html'));

		/*	Plugin hooks
		-----------------------------------------------*/
		//remove_filter('comments_template', 'dsq_comments_template');
		remove_action('loop_end', 'dsq_loop_end');
		remove_action('wp_head', 'wp_dlmp_l10n_style' );
		remove_action('wp_print_styles', 'wp_dlmp_styles');
	}

	public function init() {
		$this->lockout(WWWROOT . '/landingpage/index.html', true);

		register_post_type('works',
			array(
				'labels' => array(
					'name' => '制作実績',
					'singular_name' => '制作実績'
				),
				'public' => true,
				'menu_position' => 5,
				'query_var' => false,
				'has_archive' => true,
				'rewrite' => array('with_front' => false),
				'supports' =>array(
					'title',
					'editor',
					'thumbnail',
				)
			)
		);
		register_taxonomy(
			'works_cat',
			'works',
			array(
				'label' => 'Category',
				'hierarchical' => false,
				'rewrite' => array(
					'slug' => 'works',
					'with_front' => false
				)
			)
		);
	}

	public function tpl_param($params) {
		global $post;

		if (is_singular()) {
			$params['headcode'] = safe_value(get_post_meta($post->ID, '_crst_headcode', true), null);

			$params['modules'] = safe_value(get_post_meta($post->ID, '_crst_module', true), array(), 'array');
			$params['modules'] = array_unique($params['modules']);
			natsort($params['modules']);

			if (!empty($params['modules']))
				$params['modules__out'] = '<script>require(\'' . implode('\', \'', $params['modules']) . '\');</script>';
		}

		return $params;
	}

	public function head_tag() {
		tpl_param('modules__out');
		tpl_param('headcode');
	}

	public function admin_bar_menu($wp_admin_bar) {
		$wp_admin_bar->remove_node('themes');
		$wp_admin_bar->remove_node('customize');

		$wp_admin_bar->add_node(array(
			'id' => 'sample',
			'title' => 'サンプル',
			'href' => home_url('/sample'),
			'parent' => 'site-name'
		));
		$wp_admin_bar->add_node(array(
			'id' => 'edit',
			'title' => '投稿一覧',
			'href' => admin_url('/edit.php'),
			'parent' => 'site-name'
		));
	}

	public function the_content($content) {
		$pairs = array();

		if (get_option('twitter'))
			$pairs['@twitter'] = vsprintf('<a href="http://twitter.com/%s" target="_blank">@%1$s</a>', get_option('twitter'));

		if (get_option('facebook'))
			$pairs['@facebook'] = vsprintf('<a href="%s" target="_blank">%1$s</a>', get_option('facebook'));

		return strtr($content, $pairs);
	}

	public function the_tags($tags) {
		if (empty($tags))
			$tags = 'no tag';

		return $tags;
	}

	public function pagenation_html($html, $obj) {
		extract($obj);

		if ($class == ' page-old')
			$name = '&gt;';
		elseif ($class == ' page-new')
			$name = '&lt;';

		return "<li><a href=\"$link\" title=\"$title\" class=\"btn$class\">$name</a></li>";
	}

	public function bloginfo($output, $show = '') {
		if ($show == 'language')
			$output = 'ja';
	}
}

new my_function_php();

