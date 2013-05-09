<?php get_header(); ?>
<div class="container">
	<aside class="content content-ground">
		<form action="<?php esc_attr_e(home_url('/')); ?>" class="form-inline txt-center">
			<input type="text" name="s" value="<?php echo esc_html(get_query_var('s')); ?>" class="text span-6" placeholder="キーワード" />
			<button class="btn btn-blue" type="submit">検索</button>
			<select name="post_type" class="span-2">
				<option value="post"<?php _selected(get_query_var('post_type'), 'post'); ?>>ブログ</option>
				<option value="page"<?php _selected(get_query_var('post_type'), 'page'); ?>>一般ページ</option>
			</select>
		</form>
		<script>require('form');</script>
	</aside>
<?php
	if (have_posts()):
?>
	<div class="content content-box">
		<p class="lead">&mdash; 全<?php esc_html_e($wp_query->found_posts); ?>件 &mdash;</p>
<?php
		while (have_posts()):
			the_post();
?>
		<hr class="flush-lr" />
		<article class="clear-after">
			<div class="span-10">
				<header class="post-header">
					<h3 class="no-margin"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
					<p class="txt-small note"><small><?php the_permalink(); ?></small></p>
				</header>
				<p><?php crst_power_search::the_content(); ?></p>
			</div>
			<div class="span-2 txt-center">
<?php
			if (has_post_thumbnail()):
?>
				<a href="<?php the_permalink(); ?>" class="block thumb scale70-lt480"><?php the_post_thumbnail('thumbnail'); ?></a>
<?php
			else:
?>
				<img src="http://cambelt.co/243x172/No Image?color=888888,ffffff" alt="" class="thumb scale70-lt480" />
<?php
			endif;
?>
			</div>
			<br class="clear" />
		</article>
<?php
		endwhile;
?>
	<!--/ .content --></div>
<?php
		if ($wp_query->max_num_pages > 0):
?>
	<div id="page-navigation">
		<ul class="compact menu">
<?php
			echo code_indent(Pagenation::get(), 3);
?>
		</ul>
	<!--/ #page-navigation --></div>
<?php
		endif;
	elseif (!get_query_var('is_search_box')):
?>
	<div class="content content-ground">
		<p class="lead">記事が見つかりませんでした<br />ほかの言葉で検索すると見つかるかもしれません</p>
	<!--/ .content --></div>
<?php
	endif;
?>
<!--/ .container --></div>
<?php get_footer(); ?>