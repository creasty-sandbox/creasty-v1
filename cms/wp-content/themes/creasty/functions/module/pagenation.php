<?php

class Pagenation {
	public function get() {
		global $wp_query;

		$current = max(1, get_query_var('paged'));
		$max = max(1, $wp_query->max_num_pages);
		return self::make($current, $max);
	}
	private function make($current, $pagemax, $entries = 10, $edge = 2) {
		$html = array();

		$ne_half = ceil($entries / 2);
		$upper_limit = $pagemax - $entries + 1;
		$start = $current > $ne_half ? max(min($current - $ne_half, $upper_limit), 1) : 1;
		$end = $current > $ne_half ? min($current + $ne_half - 1, $pagemax) : min($entries, $pagemax);

		$start = ($start > $edge + 1) ? $start : 1;
		$end = ($end < $pagemax - $edge) ? $end : $pagemax;

		// Prev
		if ($current > 1) {
			$html[] = self::entriy_html($current - 1, 'page-new', '&laquo;', '新しい記事へ');
		}
		// Starting Point
		if ($start > $edge + 1) {
			for ($i = 1; $i <= $edge; $i++) {
				$html[] = self::entriy_html($i, $current);
			}
			$html[] = '<li class="ellipsis">...</li>';
		}

		// Entries
		for ($i = $start; $i <= $end; $i++) {
			$html[] = self::entriy_html($i, $current);
		}

		// Ending Point
		if ($end < $pagemax) {
			$html[] = '<li class="ellipsis">...</li>';
			for ($i = $pagemax - $edge + 1; $i <= $pagemax; $i++) {
				$html[] = self::entriy_html($i, $current);
			}
		}
		//Next
		if ($current < $pagemax) {
			$html[] = self::entriy_html($current + 1, 'page-old', '&raquo;', '古い記事へ');
		}

		return implode("\n", $html);
	}
	public function entriy_html($num, $class, $name = '', $title = '') {
		if (is_numeric($class) && $class == $num)
			$class = ' active';
		elseif (is_string($class))
			$class = ' ' . $class;
		else
			$class = '';

		$name = empty($name) ? $num : $name;
		$title = empty($title) ? $num . 'ページへ' : $title;
		$link = esc_url(get_pagenum_link($num));

		$html = "<li><a href=\"$link\" title=\"$title\" class=\"btn$class\">$name</a></li>";

		return apply_filters('Pagenation::entriy_html', $html, array(
			'num' => $num,
			'class' => $class,
			'name' => $name,
			'title' => $title,
			'link' => $link
		));
	}
}

