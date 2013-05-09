<?php get_header(); ?>
<div class="container">
	<div role="main" class="content content-ground">
		<ul id="works">
<?php
	if (have_posts()):
		while (have_posts()):
			the_post();

?>
			<li class="showcase-item">
				<a href="<?php the_permalink(); ?>" class="fancy-thumb">
					<div class="caption">
<?php
			if (has_post_thumbnail()):
?>
						<?php the_post_thumbnail('thumbnail'); echo "\n"; ?>
<?php
			else:
?>
						<img src="http://cambelt.co/243x172/<?php echo urlencode(get_the_title()); ?>?color=888888,ffffff" alt="" />
<?php
			endif;
?>
						<h4 class="caption-title"><?php the_title(); ?></h4>
					</div>
				</a>
			</li>
<?php
		endwhile;
	endif;
?>
		</ul>
		<br class="clear" />

		<script>
			require(
				'&slider',
				'module.jquery.fluidGrid',
				'module.jquery.showcase',
				'!ready'
			).done(function () {
				$('#works')
				.show()
				.fluidGrid({
					columns: 4,
					gutter: 20,
					minWidth: 100
				})
				.find('> li > a')
				.showcase({
					panel: '<div id="showcase-panel" class="content content-box" />',
					valley: '.grid-clear',
					callback: function ($panel_content, init) {
						if (init) {
							$panel_content
							.find('.dyn-slider')
							.slider({
								type:      'bullets',
								width:     '767px',
								height:    '426px',
								pauseTime: 7000
							});
						}
					}
				});

			});
		</script>

		<a id="start-new-project" href="/about/contact">
			<div>お問い合わせ &amp; 無料見積もり</div>
		</a>
	<!--/ .content --></div>
<!--/ .container --></div>
<?php get_footer(); ?>