<?php
/*
Template Name: Portal
*/

get_header();
?>
<div class="container">
	<div class="content">
<?php
	if (have_posts()):
		the_post();

		if (has_post_thumbnail()):
?>
		<div id="hero">
			<?php the_post_thumbnail('hero-image'); echo "\n"; ?>
		</div>
<?php
		endif;

		BeautifyCode::the_content(2);
		echo "\n";

	endif;
?>
	<!--/ .content --></div>
<!--/ .container --></div>
<?php get_footer(); ?>