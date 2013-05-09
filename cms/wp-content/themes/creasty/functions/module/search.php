<?php

add_action('parse_query', 'parse_query_search');
function parse_query_search($query) {
	if(!is_search())
		return;

	$post_type = $_REQUEST['post_type'];

	if (isset($post_type) && !empty($post_type))
		set_query_var('post_type', $post_type);
	else
		set_query_var('post_type', 'post');
}

/*	検索まわり
-----------------------------------------------*/
add_action('parse_query', 'parse_query_plus');
function parse_query_plus($query) {
	global $wp_query;

	$s = $wp_query->query['s'];

	if (isset($s) && empty($s)) {
		$wp_query->post_count = 0;
		$wp_query->posts = array();
		set_query_var('is_search_box', 'true');
	}
}
add_filter('posts_search', 'custom_search', 10, 2);
function custom_search($search, $wp_query) {
	$s = $wp_query->query['s'];

	if (isset($s))
		$wp_query->is_search = true;

	return $search;
}
add_action('parse_request', 'parse_request_plus');
function parse_request_plus($wp) {
	if ($wp->query_vars['s']) {
		$s = &$wp->query_vars['s'];
		$s = mb_convert_kana($s, 'saKHV', 'UTF-8');
		$s = strip_tags($s);
		$s = trim($s);
		$s = preg_replace('|\s+|u' ,' ', $s);
	}
}


class crst_power_search {
	public function the_content($len = 300) {
		global $post;

		$s = preg_quote(get_query_var('s'));
		$s = str_replace(' ', '|', $s);

		$content = $post->post_content;

		$content = CreastyFormatting::content_short($content, 0);

		$search = $content;
		$search = preg_replace('/(' . $s . ')/ui', '<find />', $content, 1);

		$pos = (int) mb_strpos($search, '<find />');
		$pos = max(0, floor($pos - $len * 0.1));

		$top = '';
		if ($pos > 0) {
			$top_len = min($len * 0.1, $pos);
			$top = mb_substr($content, 0, $top_len);
			if ($top_len != $pos)
				$top .= '… ';
			$len *= 0.9;
		}

		$content = mb_substr($content, $pos, $len);

		$content = preg_replace('/(' . $s . ')/ui', '<i class="label label-red">$1</i>', $content);

		$content .= '…';

		echo $top . $content;
	}

}

