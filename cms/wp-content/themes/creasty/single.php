<?php get_header(); ?>
<div class="container">
	<div id="side-body">
<?php
	if (have_posts()):
		the_post();
?>
		<div role="main" id="main" class="content content-box">
			<header class="post-header">
<?php
		if (has_post_thumbnail()):
?>
				<div id="hero">
					<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('blog-image'); ?></a>
				</div>
<?php
		endif;
?>
				<ul class="compact menu tiny">
					<li class="icon icon-clock"><time datetime="<?php the_time('Y-m-d'); ?>"><?php echo get_post_time('F d, Y'); ?></time></li>
					<li class="icon icon-book"><?php the_category(', '); ?></li>
					<li class="icon icon-tag"><?php the_tags(''); ?></li>
				</ul>
			</header>
<?php
		BeautifyCode::the_content(3);
		echo "\n";
?>
			<hr class="flush-lr" />
			<aside>
				<h3 class="hide">シェアする</h3>
				<ul id="share" class="social-share"></ul>
				<script>
					require('module.jquery.socialShare', '!ready').done(function(){
						$('#share').socialShare({
							script: '/library/api/socialcounter.php',
							buttons: {
								'twitter': 'Tweet',
								'facebook': 'Facebook',
								'gplus': 'Google+',
								'hatena': 'Hatena',
								'evernote': 'Evernote'
							}
						});
					});
				</script>
			</aside>
		</div>
<?php
		crst::related_posts();
		if (have_posts()):
?>
		<article class="content content-box">
			<h3 class="icon icon-look">こんな記事も読まれています</h3>
			<ul class="link-list">
<?php
			while (have_posts()):
				the_post();
?>
				<li class="clear-after">
					<div class="span-3 txt-center">
<?php
				if (has_post_thumbnail()):
?>
						<a href="<?php the_permalink(); ?>" class="block thumb scale70-lt480"><?php the_post_thumbnail('thumbnail', 'title='); ?></a>
<?php
				else:
?>
						<img src="http://cambelt.co/243x172/No Image?color=888888,ffffff" alt="" class="thumb scale70-lt480" />
<?php
				endif;
?>
					</div>
					<div class="span-9">
						<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
						<p><?php crst::the_excerpt(); ?> <a href="<?php the_permalink(); ?>" class="more label">続きを読む</a></p>
					</div>
				</li>
<?php
			endwhile;
			wp_reset_query();
?>
			</ul>
		</article>
<?php
		endif;

		if (is_user_logged_in() && comments_open()):
?>
		<article class="content content-box">
			<h3 class="icon icon-comment">この記事に対するコメント<?php get_comments_number() > 0 ? ' (' . get_comments_number() . ')' : ''; ?></h3>
<?php
			BeautifyCode::begin();
			comments_template();
			//get_template_part('comments');
			BeautifyCode::end(3);
			echo "\n";
?>
		</article>
<?php
		endif;
	else:
?>
		<div class="content" role="main">
			<p class="lead txt-center">記事が見つかりませんでした</p>
		</div>
<?php
	endif;
?>
	<!--/ #side-body --></div>
	<div id="side-nav">
<?php get_sidebar(); ?>
	<!--/ #side-nav --></div>
<!--/ .container --></div>
<?php get_footer(); ?>