<?php

class CreastyFormatting {
	public function content_short($content, $len = 140) {
		$content = preg_replace('|(?:\[/?)[^/\]]+/?\]|s', '', $content);
		$content = preg_replace('%<(script|style)[^>]*>.+?</\1>%s', '', $content);
		$content = strip_tags($content);
		$content = wptexturize($content);
		$content = trim($content);
		$content = preg_replace('|\s+|u' ,' ', $content);
		if ($len > 0) $content = mb_substr($content, 0, $len);
		$content = preg_replace('|&#?[a-z0-9]+$|iu' ,'', $content);
		return $content;
	}

}

