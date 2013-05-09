<?php
/*
Template Name: Side nav
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
?>
		<div id="side-body">
<?php
		BeautifyCode::the_content(3);
		echo "\n";
?>
		<!--/ #side-body --></div>
		<aside id="side-nav">
			<h3><?php the_title(); ?></h3>
			<ul>
<?php
		foreach (crst::nav_menu() as $menu):
			$options = '';

			if (!empty($menu['classes']))
				$options .= ' class="' . $menu['classes'] . '"';

			if (!empty($menu['attr_title']))
				$options .= ' title="' . $menu['attr_title'] . '"';

			if (!empty($menu['target']))
				$options .= ' target="' . $menu['target'] . '"';
?>
				<li><a href="<?php echo esc_url($menu['link']); ?>"<?php echo $options; ?>><?php echo esc_html($menu['title']); ?></a></li>
<?php
		endforeach;
?>
			</ul>
		<!--/ #side-nav --></aside>
<?php
	endif;
?>
	<!--/ .content --></div>
<!--/ .container --></div>
<?php get_footer(); ?>