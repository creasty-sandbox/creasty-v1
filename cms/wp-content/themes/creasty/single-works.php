<?php

crst_page_ajax(function () {
	if (have_posts()) {
		the_post();
		BeautifyCode::the_content();
	}
});

get_header(); ?>
<div class="container">
<?php
	if (have_posts()):
		the_post();
?>
	<div role="main" class="content content-box">
<?php
			BeautifyCode::the_content(2);
			echo "\n";
?>
		<script>
			require('&slider', '!ready').done(function () {
				$('.dyn-slider').slider({
					type:      'bullets',
					width:     '767px',
					height:    '426px',
					pauseTime: 7000
				});
			});
		</script>
	</div>
	<p><a href="/works" class="box">See Other Works</a></p>
<?php
	else:
?>
	<div role="main" class="content content-ground">
		<p class="lead txt-center">記事が見つかりませんでした</p>
	</div>
<?php
	endif;
?>
<!--/ .container --></div>
<?php get_footer(); ?>