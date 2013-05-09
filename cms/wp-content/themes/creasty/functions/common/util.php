<?php

/*=== Formatting / Getting
==============================================================================================*/
function safe_value($val, $fallback = false, $option = 'none') {
	$op = true;

	switch ($option) {
		case 'array':
			$op = is_array($val);
			break;
		case 'string':
			$op = is_string($val);
			break;
		case 'number':
			$op = is_numeric($val);
			break;
		case 'bool':
			$op = is_bool($val);
			break;
	}

	if (!isset($val) || empty($val) || !$op)
		return $fallback;

	return $val;
}

function array2attr($array) {
	$buffer = '';

	foreach ($array as $key => $val) {
		if (is_string($key) && $val) {
			$key = sanitize_key($key);
			$val = esc_attr($val);
			$buffer .= " $key=\"$val\"";
		}
	}

	return $buffer;
}

function code_indent($code, $indent = 0, $relative = false) {
	$min = 0;

	if ($relative) {
		preg_match_all('|^\t*|mu', $code, $m);
		$min = strlen(min($m[0]));
	}

	if ($indent > 0)
		$code = preg_replace('|^|mu', str_repeat("\t", $indent), $code);

	if ($indent <= 0)
		$code = preg_replace('|^\t{0,' . abs($min - $indent) . '}|mu', '', $code);

	return $code;
}


/*=== Reguarize URL - Relative, Absolute, Canonical
==============================================================================================*/
function relative_url($url) {
	$base = preg_quote($_SERVER['SERVER_NAME']);
	$url = preg_replace('!https?://' . $base . '(/|$)!', '/', $url);

	if (empty($url))
		$url = '/';

	return $url;
}
function absolute_url($url) {
	if (!preg_match('|^(https?:)?//|', $url)) {
		$protocol = $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
		$url = preg_replace('!^/?!', $protocol . $_SERVER['SERVER_NAME'] . '/', $url);
	}
	return $url;
}
function canonical_url($post) {
	global $wp_rewrite, $cat;

	$url = '';
	$post_type = get_post_type() ? get_post_type() : get_query_var('post_type');
	$custom_post = ($post_type != 'post') && ($post != 'page');

	if (is_front_page()) {
		$url = home_url(user_trailingslashit($wp_rewrite->root));
	} elseif (is_home()) {
		$url = home_url(user_trailingslashit($wp_rewrite->front));
	} elseif (is_singular()) {
		$url = get_permalink();
	} elseif (is_category()) {
		$url = get_category_link($cat);
	} elseif (is_tag()) {
		$id = get_term_by('slug', get_query_var('tag'), 'post_tag')->term_id;
		$url = get_tag_link($id);
	} elseif (is_archive()) {
		if (is_date()) {
			$year = get_query_var('year');

			if (get_query_var('monthnum') > 0)
				$month = get_query_var('monthnum');

			if (get_query_var('day') > 0)
				$day = get_query_var('day');

			$url = get_day_link($year, $month, $day);
		} elseif (is_tax()) {
			$taxonomy = get_query_var('taxonomy');
			$term = get_term_by('slug', get_query_var('term'), $taxonomy);
			$url = get_term_link($term);
		} elseif ($custom_post) {
			$url = get_post_type_archive_link($post_type);
		}
	}

	$url = absolute_url($url);

	return $url;
}
function get_file_ver($file, $param = '') {
	if (file_exists($file)) {
		$ver = 'ver=' . filemtime($file);

		if (!empty($param))
			$param = "$ver&$param";
		else
			$param = $ver;
	}

	return empty($param) ? '' : "?$param";
}


/*=== Utilities
==============================================================================================*/
function get_template_info() {
	global $post;

	$tpl = get_post_meta($post->ID, '_wp_page_template', true);
	$tpl = safe_value($tpl, 'default', 'string');

	preg_match('!\-?tpl\-([\w+\-]+)\.php$!', $tpl, $m);

	return $m ? $m[1] : 'default';
}


/*=== Template Param
==============================================================================================*/
function tpl_param($key, $default = '', $option = 'none') {
	echo get_tpl_param($key, $default, $option);
}
function get_tpl_param($key, $default = false, $option = 'none') {
	global $params;

	$param = safe_value($params[$key], $default, $option);

	return $param;
}


/*=== Ajax
==============================================================================================*/
function crst_page_ajax($func) {
	if (!is_singular() || !$_GET['_ajax'])
		return;

	do_action('tpl_init');
	call_user_func($func);

	exit();
}


/*=== Checked, selected, and disabled
==============================================================================================*/
function _checked($checked, $current = true, $echo = true) {
	return __crst__checked_selected_helper($checked, $current, $echo, 'checked');
}
function _selected($selected, $current = true, $echo = true) {
	return __crst__checked_selected_helper($selected, $current, $echo, 'selected');
}
function _disabled($disabled, $current = true, $echo = true) {
	return __crst__checked_selected_helper($disabled, $current, $echo, 'disabled');
}
function __crst__checked_selected_helper($helper, $current, $echo, $type) {
	if ((string) $helper === (string) $current)
		$result = " $type=\"$type\"";
	else
		$result = '';

	if ($echo)
		echo $result;

	return $result;
}

