<?php get_header(); ?>
<div class="container">
	<div id="side-body">
<?php
	if (have_posts()):
		while (have_posts()):
			the_post();
?>
		<article class="content content-box">
			<header class="post-header">
				<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
<?php
			if (has_post_thumbnail()):
?>
				<div class="flush-lr">
					<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('blog-image'); ?></a>
				</div>
<?php
			endif;
?>
				<ul class="compact menu tiny">
					<li class="icon icon-clock"><time datetime="<?php the_time('Y-m-d'); ?>"><?php echo get_post_time('F d, Y'); ?></time></li>
					<li class="icon icon-book"><?php the_category(', ') ?></li>
					<li class="icon icon-tag"><?php the_tags(''); ?></li>
				</ul>
			</header>
			<p class="post-excerpt"><?php crst::the_excerpt(); ?></p>
			<a href="<?php the_permalink(); ?>" class="box">続きを読む</a>
		</article>
<?php
		endwhile;

		if ($wp_query->max_num_pages > 0):
?>
		<div id="page-navigation">
			<ul class="compact menu">
<?php
			echo code_indent(Pagenation::get(), 4), "\n";
?>
			</ul>
		</div>
<?php
		endif;
	else:
?>
		<div class="content">
			<p class="lead txt-center">記事が見つかりませんでした</p>
		</div>
<?php
	endif;
?>
	<!--/ #side-body --></div>
	<div id="side-nav">
<?php get_sidebar(); ?>
	<!--/ #side-nav --></div>
</div>
<?php get_footer(); ?>