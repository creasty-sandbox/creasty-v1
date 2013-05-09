<?php

class Breadcrumb {
	static $args = array(
        'home_label'        => 'ホーム',
        'year_label'        => '%s年',
        'month_label'       => '%s月',
        'day_label'         => '%s日'
   );

	public function get_array() {
		global $post;

		$args = self::$args;

		$depth = array();
		$depth[] = array(
			'title' => $args['home_label'],
			'link' => get_bloginfo('url')
		);
		self::_posts_array($depth);

		if (is_tax()) {
			$taxonomy = get_query_var('taxonomy');
			$term = get_term_by('slug', get_query_var('term'), $taxonomy);
			$tax_obj = get_taxonomies(array('name' => $taxonomy), 'objects');
			$post_type = $tax_obj[$taxonomy]->object_type[0];

			if ($post_type != 'post' && $post_type != 'page') {
				$post_type_name = get_post_type_object($post_type);
				$depth[] = array(
					'title' => $post_type_name->labels->name,
					'link' => get_post_type_archive_link($post_type)
				);
			}

			if (is_taxonomy_hierarchical($taxonomy) && $term->parent != 0) {
				$ancestors = array_reverse(get_ancestors($term->term_id, $taxonomy));
				foreach ($ancestors as $ancestor_id) {
					$ancestor = get_term($ancestor_id, $taxonomy);
					$depth[] = array(
						'title' => $ancestor->name,
						'link' => get_term_link($ancestor, $term->slug)
					);
				}
			}
		} elseif (is_attachment()) {
			if ($post->post_parent) {
				if ($parent_post = get_post($post->post_parent))
					self::_singular_array($depth, $parent_post);
			}
			$depth[] = array('title' => $parent_post->post_title, 'link' => get_permalink($parent_post->ID));
		} elseif (is_singular() && !is_front_page()) {
			self::_singular_array($depth, $post);
		} elseif (is_category()) {
			global $cat;
			$category = get_category($cat);
			if ($category->parent != 0) {
				$ancestors = array_reverse(get_ancestors($category->term_id, 'category'));
				foreach ($ancestors as $ancestor_id) {
					$ancestor = get_category($ancestor_id);
					$depth[] = array('title' => $ancestor->name, 'link' => get_category_link($ancestor->term_id));
				}
			}
		} elseif (is_date()) {
			$year = get_query_var('year');
			$month = get_query_var('monthnum');
			$day = get_query_var('day');

			if ($year > 0)
				$depth[] = array(
					'title' => sprintf($args['year_label'], $year),
					'link' => get_year_link($year)
				);
			if ($month > 0)
				$depth[] = array(
					'title' => sprintf($args['month_label'], $month),
					'link' => get_month_link($year, $month)
				);
			if ($day > 0)
				$depth[] = array(
					'title' => sprintf($args['day_label'], $day),
					'link' => get_day_link($year, $month, $day)
				);
		}

	    return $depth;
	}

	private function _singular_array(&$depth, $post) {
		$post_type = $post->post_type;
		if (is_post_type_hierarchical($post_type)) {
			$ancestors = array_reverse(get_post_ancestors($post));

			if (!count($ancestors))
				return;

			$ancestor_posts = get_posts('post_type=' . $post_type . '&include=' . implode(',', $ancestors));
			foreach ($ancestors as $ancestor) {
				foreach ($ancestor_posts as $ancestor_post) {
					if($ancestor == $ancestor_post->ID){
						$depth[] = array(
							'title' => $ancestor_post->post_title,
							'link' => get_permalink($ancestor_post->ID)
						);
					}
				}
			}
	    } else {
			$post_type_taxonomies = get_object_taxonomies($post_type, false);

			if ($post_type != 'post' && $post_type != 'page') {
				$post_type_name = get_post_type_object($post_type);
				$depth[] = array(
					'title' => $post_type_name->labels->name,
					'link' => get_post_type_archive_link($post_type)
				);
			}

			if (!is_array($post_type_taxonomies) || !count($post_type_taxonomies))
				return;

			foreach ($post_type_taxonomies as $tax_slug => $taxonomy) {
				if (!$taxonomy->hierarchical)
					continue;

				$terms = get_the_terms($post->ID, $tax_slug);
				if (!$terms)
					continue;

				$term = array_shift($terms);

				if ($term->parent == 0) {
					$ancestors = array_reverse(get_ancestors($term->term_id, $tax_slug));
					foreach ($ancestors as $ancestor_id) {
						$ancestor = get_term($ancestor_id, $tax_slug);
						$depth[] = array(
							'title' => $ancestor->name,
							'link' => get_term_link($ancestor, $tax_slug)
						);
					}

					$depth[] = array(
						'title' => $term->name,
						'link' => get_term_link($term, $tax_slug)
					);

					break;
				}
			}
		}
	}

	private function _posts_array(&$depth) {
		if (is_page() || is_front_page() || is_404()) {
			return;
		} elseif (is_tax()) {
			$tax = get_taxonomy(get_query_var('taxonomy'));
			if (count($tax->object_type) != 1 || $tax->object_type[0] != 'post')
				return;
		} elseif (is_home() && !get_query_var('pagename')) {
			return;
		} elseif (!is_category() && !is_tag()) {
			$post_type = get_query_var('post_type') ? get_query_var('post_type') : 'post';
			if ($post_type != 'post')
				return;
		}

		if (!is_home() && get_option('show_on_front') == 'page' && $posts_page_id = get_option('page_for_posts')) {
			$posts_page = get_post($posts_page_id);
			$depth[] = array(
				'title' => $posts_page->post_title,
				'link' => get_permalink($posts_page->ID)
			);
		}
	}

}

