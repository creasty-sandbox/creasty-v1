<?php

/*=== Embed JS Fiddle
==============================================================================================*/
add_shortcode('jsfiddle', 'sh_jsfiddle_func');
function sh_jsfiddle_func($atts){
	extract(shortcode_atts(array(
		'url' => '',
		'width' => '100%',
		'height' => '300',
		'include' => 'result,html,js,css'
	), $atts));

	if(empty($url))
		return '';

	$width = preg_replace('/(\d+)(?!%|px)$/', '$1px', $width);
	$height = preg_replace('/(\d+)(?!%|px)$/', '$1px', $height);

	$html = <<<HTML
		<div class="embed" style="width: {$width}; height: {$height};">
			<div class="embed-inner">
				<iframe src="http://jsfiddle.net/{$url}/embedded/{$include}"></iframe>
			</div>
		</div>
HTML;

	return $html;
}


/*=== Embed Youtube
==============================================================================================*/
add_shortcode('youtube', 'sh_youtube_func');
function sh_youtube_func($atts){
	extract(shortcode_atts(array(
		'url' => '',
		'width' => '640',
		'height' => '360'
	), $atts));

	if(empty($url))
		return '';

	$width = preg_replace('/(\d+)(?!%|px)$/', '$1px', $width);
	$height = preg_replace('/(\d+)(?!%|px)$/', '$1px', $height);

	$html = <<<HTML
		<div class="embed" style="width: {$width}; height: {$height};">
			<div class="embed-inner">
				<iframe src="http://www.youtube.com/embed/{$url}" frameborder="0" allowfullscreen></iframe>
			</div>
		</div>
HTML;

	return $html;
}


/*=== Eval PHP Code
==============================================================================================*/
add_shortcode('php', 'sh_php_func');
function sh_php_func($atts, $content){
	ob_start();
	eval($content);
	$render = ob_get_contents();
	ob_end_clean();
	return $render;
}


/*=== Include External Files
==============================================================================================*/
add_shortcode('include', 'sh_include_func');
function sh_include_func($atts){
	extract(shortcode_atts(array(
		'url' => '',
		'noindent' => false,
		'noautop' => false
	), $atts));

	if(empty($url))
		return '';

	$url = WWWROOT . $url;

	if(!file_exists($url) || !is_readable($url))
		return '<span href="#" style="color: red !important;">[!読み込めないファイル]</span>';

	if(preg_match('/\.php$/', $url)){
		ob_start();
		include($url);
		$content = ob_get_contents();
		ob_end_clean();
	}else{
		$content = file_get_contents($url);
	}

	return $content;
}


/*=== QR Code API
==============================================================================================*/
add_shortcode('qrcode', 'sh_qrcode_func');
function sh_qrcode_func($atts){
	extract(shortcode_atts(array(
		'url' => 'http://www.creasty.com',
		'size' => '80',
	), $atts));

	$size = (int) $size;
	$url_u = esc_url($url);
	$url_a = esc_attr($url);

	return vsprintf(
		'<img src="https://chart.googleapis.com/chart?chs=%dx%1&cht=qr&chl=%s&choe=UTF-8" alt="%s" />',
		$size,
		$url_u,
		$url_a
	);
}


/*=== Screenshot API
==============================================================================================*/
add_shortcode('screenshot', 'sh_screenshot_func');
function sh_screenshot_func($atts){
	extract(shortcode_atts(array(
		'url' => 'http://www.creasty.com',
		'size' => '400',
	), $atts));

	$size = (int) $size;

	return vsprintf(
		'<img src="http://s.wordpress.com/mshots/v1/%s?w=%d" alt="%s" />',
		urlencode(esc_url($url)),
		$size,
		esc_attr($url)
	);
}


/*=== Blog Site Shot
==============================================================================================*/
add_shortcode('siteshot', 'sh_siteshot_func');
function sh_siteshot_func($atts, $content = '') {
	$info = preg_split('|(\n\r?)+|', trim($content));

	$info = array_map('trim', $info);

	if (sizeof($info) == 3)
		array_splice($info, 2, 0, '');

	list($title, $url, $desc, $image) = $info;

	$link = $url;
	$link = preg_replace('|^https?://|', '', $link);
	$link = preg_replace('|/$|', '', $link);

	$title = esc_html($title);
	$desc = empty($desc) ? '' : (esc_html($desc) . "\n<br />");
	$url = esc_url($url);
	$link = esc_html($link);

	$tag = $atts['title'] ? $atts['title'] : 'h3';

	$html = <<<EOD
		<$tag>$title</$tag>

		<p>$desc<a href="$url" target="_blank" class="external">$link</a></p>

		<a href="$url" target="_blank" class="block thumb">
			$image
		</a>
EOD;

	return $html;
}

/*=== Layout Columns
==============================================================================================*/
add_shortcode('col', 'sh_col_func');
function sh_col_func($atts, $content = ''){
	$defaults = array(
		0 => 'a',
		1 => 'a',
		'class' => ''
	);

	$atts += $defaults;

	$len0 = strlen($atts[0]);
	$len1 = strlen($atts[1]);

	if($len0 <= 1 || $len0 < $len1)
		return $content;

	$content = do_shortcode($content);

	$atts['class'] = trim("col-{$atts[0]}-{$atts[1]} " . $atts['class']);

	$attr = array2attr($atts);

	$content = "<div$attr>\n$content\n</div>";

	if(substr($atts[0], -$len1) == $atts[1])
		$content .= "\n<br class=\"clear\" />\n";

	return $content;
}

add_shortcode('right', 'sh_right_func');
function sh_right_func($atts, $content = ''){
	$content = do_shortcode($content);

	$atts['class'] = trim('col-right' . $atts['class']);

	$attr = array2attr($atts);

	$content = "<div$attr>\n$content\n</div>";

	return $content;
}


/*=== [post_type=works] Category List
==============================================================================================*/
add_shortcode('works_category_list', 'sh_works_category_list_func');
function sh_works_category_list_func($atts){
	global $post;

	$terms = get_the_terms($post->ID, 'works_cat');

	if (!$terms)
		return;

	$render = "<ul class=\"list\">\n";
	foreach($terms as $term){
		$name = esc_html($term->name);
		$render .= "\t<li class=\"icon icon-check\">$name</li>\n";
	}
	$render .= '</ul>';

	return $render;
}


/*=== [Plugin] Twitter Blackbird Pie
==============================================================================================*/
add_filter('bbp_create_tweet', function($tweet_details, $options = array()){
	global $post;

	// PROFILE DATA
	$name = $tweet_details['screen_name'];
	$real_name = $tweet_details['real_name'];
	$profile_pic = esc_url($tweet_details['profile_pic']);
	$profile_pic = preg_replace('!_(normal|bigger|mini|original)\.(jpe?g|png|gif)$!i', '_bigger.$2', $profile_pic);

	$profile_url = esc_url("http://twitter.com/{$name}");

	// GENERAL INFO
	$id = $tweet_details['id'];
	$url = esc_url("http://twitter.com/#!/{$name}/status/{$id}");

	// TIME INFO
	$time = $tweet_details['time_stamp'];
	$date = date(
		get_option('date_format') . ' ' . get_option('time_format'),
		$time + (get_option('gmt_offset') * 3600)
	);

	// Tweet Action Urls
	$retweet_url = 'https://twitter.com/intent/retweet?tweet_id='. $id;
	$reply_url = 'https://twitter.com/intent/tweet?in_reply_to=' . $id;
	$favorite_url = 'https://twitter.com/intent/favorite?tweet_id=' . $id;

	// If we have a Twitter handle for this post author then we can mark them as 'related' to this tweet
	$handle = get_option('twitter', true);
	if($handle && trim($handle) != ''){
		$retweet_url .= "&amp;related=$handle";
		$reply_url .= "&amp;related=$handle";
		$favorite_url .= "&amp;related=$handle";
	}

	$tweet = $tweet_details['tweet_text'];

	$tweetHTML = <<<EOD
		<div>
			<blockquote>
				<div class="pullout-left po-80">
					<a href="{$profile_url}" target="_blank" class="block pullout-item">
						<img src="{$profile_pic}" />
					</a>
					<p class="txt-left">{$tweet}</p>
					<ul class="compact menu tiny">
						<li class="icon icon-twitter"><a href="{$profile_url}" target="_blank">@{$name}: {$real_name}</a></li>
						<li class="icon icon-clock"><a href="{$url}" target="_blank">{$date}</a></li>
					</ul>
					<ul class="compact menu tiny">
						<li class="icon icon-quote"><a href="{$reply_url}" target="_blank" title="返信する">返信</a></li>
						<li class="icon icon-replay"><a href="{$retweet_url}" target="_blank" title="リツイートする">リツイート</a></li>
						<li class="icon icon-star"><a href="{$favorite_url}" target="_blank" title="お気に入りに登録する">お気に入り</a></li>
					</ul>
				</div>
			</blockquote>
		</div>
EOD;

	return $tweetHTML;
});

