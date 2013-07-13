		<aside class="follow">
			<a href="<?php bloginfo('rss2_url'); ?>" rel="alternate" class="btn btn-cta btn-rss icon-rss">
				Subscribe Blog
			</a>
			<a href="http://twitter.com/<?php echo get_option('twitter'); ?>" class="btn btn-cta btn-twitter icon-twitter">
				Follow on Twitter
			</a>
			<a href="<?php echo get_option('facebook'); ?>" class="btn btn-cta btn-facebook icon-facebook">
				Find Me on Facebook
			</a>
		</aside>
		<aside class="search">
			<h3 class="icon-search">Search for</h3>
			<form action="<?php esc_attr_e(home_url('/')); ?>" method="get" class="form-inline">
				<input type="text" name="s" class="text" placeholder="keywords" />
				<button type="submit" class="btn">Go</button>
			</form>
			<script>require('form');</script>
		</aside>
		<aside>
			<h3 class="icon-book">Categories</h3>
			<ul>
<?php
		foreach (get_categories() as $cat):
?>
				<li><a href="<?php echo get_category_link($cat->term_id); ?>"><?php echo esc_html($cat->name); ?></a></li>
<?php
		endforeach;
?>
			</ul>
		</aside>
		<aside>
			<h3 class="icon-write">Latest Articles</h3>
			<ul>
<?php
		query_posts('posts_per_page=5&orderby=date');
		while (have_posts()):
			the_post();
?>
				<li><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></li>
<?php
		endwhile;
		wp_reset_query();
?>
			</ul>
		</aside>
		<aside>
			<h3 class="icon-shuffle">Random Inspirations</h3>
			<ul>
<?php
		query_posts('posts_per_page=5&orderby=rand');
		while (have_posts()):
			the_post();
?>
				<li><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></li>
<?php
		endwhile;
		wp_reset_query();
?>
			</ul>
		</aside>
		<aside class="facebook">
			<h3 class="icon-facebook">Facebook</h3>
			<div class="embed">
				<iframe src="//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fcreasty.web&amp;width=270&amp;height=258&amp;show_faces=true&amp;colorscheme=light&amp;stream=false&amp;border_color=%23d7d7d7&amp;header=false&amp;appId=423468787678045" scrolling="no" frameborder="0" style="border:0; overflow:hidden; width:100%; height:258px;" allowTransparency="true"></iframe>
			</div>
		</aside>
