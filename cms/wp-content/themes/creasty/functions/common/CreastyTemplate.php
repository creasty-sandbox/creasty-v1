<?php

// alias
class_alias('CreastyTemplate', 'crst');

class CreastyTemplate {
	public function title_array() {
		global $wp_query, $cat, $tag_id;

		$title = array();
		$post_type = get_post_type() ? get_post_type() : get_query_var('post_type');
		$post_type_name = get_post_type_object($post_type)->labels->name;

		if (is_single() || is_search()) {
			if ($post_type == 'post')
				$title['depth'] = 'ブログ';
			elseif ($post_type != 'page')
				$title['depth'] = $post_type_name;
		}

		if (is_front_page()) {
			$title['after'] = get_bloginfo('description');
			$title['doc'] = get_bloginfo('name') . ' &#8226; ' . get_bloginfo('description');
		} elseif (is_home()) {
			$title['main'] = 'ブログ';
		} elseif (is_single()) {
			$title['main'] = get_the_title();
		} elseif (is_page()) {
			$title['main'] = get_the_title();
		} elseif (is_archive()) {
			if (is_category()) {
				$cat_name = get_category($cat)->name;
				$title['main'] = array('カテゴリー', $cat_name);
			} elseif (is_tag()) {
				$tag_name = get_tag($tag_id)->name;
				$title['main'] = array('タグ', $tag_name);
			} elseif (is_date()) {
				$date = get_query_var('year') . '年';

				if (get_query_var('monthnum') > 0)
					$date .= get_query_var('monthnum') . '月';
				if (get_query_var('day') > 0)
					$date .= get_query_var('day') . '日';

				$title['main'] = $date;
				$title['doc'] = $date . 'の記事';
			} elseif (is_tax()) {
				$taxonomy = get_query_var('taxonomy');
				$term = get_term_by('slug', get_query_var('term'), $taxonomy);

				$title['main'] = array('カテゴリー', $term->name);
			} elseif ($post_type != 'post' && $post_type != 'page') {
				$title['main'] = $post_type_name;
			}
		} elseif (is_404()) {
			$title['main'] = 'ページが見つかりません';
			$title['doc'] = '何かお探しですか？';
		}

		if (is_search()) {
			$searchparam = esc_html(get_query_var('s'));

			$title['main'] = array('サイト内検索');
			$title['doc'] = '検索';

			if (!empty($searchparam)) {
				$title['main'][1] = '&ldquo;' . $searchparam . '&rdquo;';
				$title['doc'] .= ': &ldquo;' . $searchparam . '&rdquo;';
			}
		}

		if (is_paged())
			$title['sub'] = get_query_var('paged') . 'ページ';

		$title = apply_filters('title_array', $title);

		if (!isset($title['doc'])) {
			if (is_string($title['main']))
				$title['doc'] = $title['main'];
			elseif (is_array($title['main']))
				$title['doc'] = implode(': ', $title['main']);
		}

		return $title;
	}

	public function title_from_array($ary) {
		/*	Structure

			main-1     main-2      sub    depth      site name     after
			Aaaaaaaaa: Bbbbbbbbb - Cccc | Dddddddd | Eeeeeeeeeee * Ffffffffff
		*/

		if (!is_array($ary))
			return $ary;

		extract(array_merge(array(
			'main' => '',
			'sub' => '',
			'depth' => '',
			'after' => ''
		), $ary));

		$title = array();

		if (isset($main) && !empty($main)) {
			if (is_string($main))
				$title[] = $main;
			else
				$title[] = implode(': ', $main);
		}

		if (isset($sub) && !empty($sub)) {
			$title[] = '-';
			$title[] = $sub;
		}

		if (isset($depth) && !empty($depth)) {
			$title[] = '|';
			$title[] = $depth;
		}

		if (sizeof($title) > 0)
			$title[] = '|';

		$title[] = get_bloginfo('name');

		if (isset($after) && !empty($after)) {
			$title[] = '&#8226;';
			$title[] = $after;
		}

		return implode(' ', $title);
	}

	/**
	 * Display body id
	 */
	public function body_id() {
		$id = self::get_body_id();
		echo 'id="', $id, '"';
	}

	/**
	 * Generate body id
	 */
	public function get_body_id() {
		global $post, $cat, $tag_id;
		$post_type = get_post_type() ? get_post_type() : get_query_var('post_type');

		if (is_single() || is_page()) {
			$post_name = $post->post_name;

			if (!$post_name || strpos($post_name, '%') !== false)
				$post_name = $post->ID;

			$id = esc_attr($post_type . '-' . $post_name);
		} elseif (is_attachment()) {
			$id = 'page-archive';
		} elseif (is_front_page()) {
			$id = 'page-home';
		} elseif (is_home()) {
			$id = 'page-blog';
		} elseif (is_archive()) {
			if (is_category()) {
				$slug = get_category($cat)->slug;
				$id = 'category-' . esc_attr($slug);
			} elseif (is_tag()) {
				$slug = get_tag($tag_id)->slug;
				$id = 'tag-' . esc_attr($slug);
			} elseif (is_date()) {
				$id = 'archive-date';
			} elseif (is_tax()) {
				$tax = get_query_var('taxonomy');
				$id = 'tax-' . esc_attr($tax);
			} elseif ($post_type != 'post' && $post_type != 'page') {
				$id = 'archive-' . esc_attr($post_type);
			} else {
				$id = 'page-archive';
			}
		} elseif (is_404()) {
			$id = 'page-404';
		} elseif (is_search()) {
			$id = 'page-search';
		} else {
			$id = 'page';
		}

		return apply_filters('crst::get_body_id', $id);
	}

	/**
	 * Display the classes for the body element
	 *
	 * @see original located in `wp-includes/post-template.php`
	 */
	public function body_class($class = '') {
		$classes = self::get_body_class($class);

		if (empty($classes))
			return;

		echo 'class="', implode(' ', $classes), '"';
	}
	/**
	 * Retrieve the classes for the body element as an array
	 *
	 * @see original located in `wp-includes/post-template.php`
	 */
	public function get_body_class($class = '') {
		global $wp_query;

		$post_type = is_search() ? get_query_var('post_type') : get_post_type();

		$classes = array();

		if (is_singular())
			$classes[] = 'id-' . get_the_ID();

		if (!empty($post_type))
			$classes[] = 'type-' . $post_type;

		if (is_single() || is_page())
			$classes[] = 'page-single';
		if (is_archive())
			$classes[] = 'page-archive';
		if (is_date())
			$classes[] = 'page-date';
		if (is_category())
			$classes[] = 'page-category';
		if (is_tag())
			$classes[] = 'page-tag';
		if (is_tax())
			$classes[] = 'page-tax';
		if (is_front_page() || is_home() || is_search() || is_404())
			$classes[] = 'page-special';

		if ($wp_query->max_num_pages > 0)
			$classes[] = 'page-entries';

		$classes[] = 'tpl-' . get_tpl_param('template');

		if (!empty($class)) {
			if (!is_array($class))
				$class = preg_split('#\s+#', $class);
			$classes = array_merge($classes, $class);
		} else {
			$class = array();
		}

		$classes = array_map('esc_attr', $classes);

		return apply_filters('crst::get_body_class', $classes, $class);
	}

/*
	public function post_class($common =''){
		echo self::get_post_class(null, $common);
	}
	public function get_post_class($post = null, $common = ''){
		if(!$post)
			$post = &$GLOBALS['post'];

		if(!empty($common))
			$cls = explode(' ', $common);
		else
			$cls = array();

		if(self::is_new($post)){
			$cls[] = 'post-new';
		}
		if(self::is_updated($post)){
			$cls[] = 'post-updated';
		}
		if(!empty($post->post_password)){
			$cls[] = 'post-protected';
		}
		if(isset($post->post_status) && 'private' == $post->post_status){
			$cls[] = 'post-private';
		}

		$cat = get_the_category();
		$cat = $cat[0];
		$cls[] = 'category-' . sanitize_html_class($cat->category_nicename);

		return implode(' ', $cls);
	}
*/

	function the_excerpt($len = 140, $forcelen = false, $ellipsis = true) {
		echo self::get_the_excerpt(null, $len, $forcelen, $ellipsis);
	}
	function get_the_excerpt($post = null, $len = 140, $forcelen = false, $ellipsis = true) {
		if (!$post)
			$post = &$GLOBALS['post'];

		if (empty($post->post_excerpt)) {
			$content = $post->post_content;
		} else {
			$content = $post->post_excerpt;
			$ellipsis = false;
		}

		if ($moretag = strpos($content, '<!--more-->')) {
			$content = CreastyFormatting::content_short($content, 0);
			if ($forcelen) $content = mb_substr($content, 0, $moretag);
			$ellipsis = false;
		} else {
			$content = CreastyFormatting::content_short($content, $len);
		}

		if ($ellipsis) $content .= '…';

		return $content;
	}

	public function is_new(&$post) {
		$post_date = $post->post_date;
		$days = absint(get_option('new_days', 7));
		return self::is_widthin_days($post_date, $days);
	}
	public function is_updated(&$post) {
		$post_date = $post->post_modified;
		$days = absint(get_option('modified_days', 7));
		return self::is_widthin_days($post_date, $days);
	}
	private function is_widthin_days($post_date, $days = 7) {
		if (in_array(strtotime($post_date), array(false, -1)))
			return false;

		$limit = current_time('timestamp') - ($days - 1) * 24 * 3600;

		if (mysql2date('Y-m-d', $post_date) >= date('Y-m-d', $limit))
			return true;

		return false;
	}

	public function related_posts($limit = 5) {
		$ID = get_the_ID();

		//$catID = wp_get_post_categories($post->ID, array('fields' => 'ids'));
		//$catID = $catID[0];

		$tags = wp_get_post_tags($ID);

		if (!$tags)
			return;

		$tagIDs = array();
		foreach ($tags as $tag) {
			$tagIDs[] = $tag->term_id;
		}

		return query_posts(array(
			//'cat'               => $catID,
			'tag__in'             => $tagIDs,
			'post__not_in'        => array($ID),
			'showposts'           => $limit,
			'ignore_sticky_posts' => 0,
		));
	}

	public function nav_menu($menu_name = '') {
		if (empty($menu_name) && is_page()) {
			global $post;

			$prefix = '$';
			$pance = get_post_ancestors($post);

			if (empty($pance))
				$menu_name = $prefix . $post->post_name;
			else
				$menu_name = $prefix . get_post(array_pop($pance))->post_name;
		}

		$menu = wp_get_nav_menu_object($menu_name);

		if ($menu && !is_wp_error($menu) && !isset($menu_items))
			$menu_items = wp_get_nav_menu_items($menu->term_id);

		if (!$menu || is_wp_error($menu))
			return false;

		$nav_menu = array();

		foreach ((array) $menu_items as $key => $item) {
			$nav_menu[$item->menu_order] = array(
				'title' => $item->title,
				'description' => $item->description,
				'link' => relative_url($item->url),
				'attr_title' => $item->attr_title,
				'target' => $item->target,
				'classes' => implode(' ', $item->classes)
			);
		}

		unset($menu_items);

		return $nav_menu;
	}

}

