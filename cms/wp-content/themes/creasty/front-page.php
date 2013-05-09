<?php
add_action('wp_head', function () {
	echo '<script src="/images/home/home.js"></script>';
});

get_header();
?>
<div id="home-hero">
	<header class="container">
		<h2>
			<span>People say, I'm obsessed
			<br />by design and code</span>
			<em>I’m Yuki Iwanaga, a young web designer &amp; developer</em>
		</h2>
 	</header>
 	<div id="fader"></div>
</div>
<div id="service">
	<div class="container">
		<div class="content content-ground">
			<div class="col-abc-a">
				<img src="/images/service/services_mobile.png" alt="" class="aligncenter" />
				<h4>Web サイト制作</h4>
				<p>企業サイト、ポートフォリオサイト、ブログサイトを学生ならではの低価格で制作します。サイトの更新や管理が簡単になる WordPress の導入や、スマートフォンへの対応もやっております。</p>
			</div>
			<div class="col-abc-b">
				<img src="/images/service/services_logo.png" alt="" class="aligncenter" />
				<h4>ロゴの制作</h4>
				<p>企業のロゴ、サービスのロゴをデザインします。シンプルで可視性の高い、シンボルやタイプベースのロゴを制作します。ガイドライン付きなので統一感を持たせて運用することができます。</p>
			</div>
			<div class="col-abc-c">
				<img src="/images/service/services_coding.png" alt="" class="aligncenter" />
				<h4>コーディング業務委託</h4>
				<p>デザインデータをもとに、Web サイトで見ることが出来る HTML + CSS に変換します。SEO 対策はもちろんのこと、汎用的で将来性のあるコードを心がけています。WordPress のテーマ作成もお任せ下さい。</p>
			</div>
			<br class="clear" />

			<p><a href="/service" class="box">See More Service</a></p>
		</div>
	</div>
</div>
<div class="container">
	<section class="content content-ground">
		<h3 class="ribbon"><a href="/works">Works</a></h3>
		<ul class="home-grid">
<?php
	query_posts('post_type=works&posts_per_page=4&orderby=rand');

	while (have_posts()):
		the_post();
?>
			<li>
				<a href="<?php the_permalink(); ?>" class="fancy-thumb">
					<div class="caption">
<?php
		if (has_post_thumbnail()):
?>
						<?php the_post_thumbnail('thumbnail', 'title='); echo "\n"; ?>
<?php
		else:
?>
						<img src="http://cambelt.co/243x172/No Image?color=888888,ffffff" alt="" />
<?php
		endif;
?>
						<h4 class="caption-title"><?php the_title(); ?></h4>
					</div>
				</a>
			</li>
<?php
	endwhile;
	wp_reset_query();
?>
		</ul>
	<!--/ .content --></section>
	<section class="content content-ground">
		<h3 class="ribbon"><a href="/blog">Blog Articles</a></h3>
		<ul class="home-grid">
<?php
	query_posts('posts_per_page=4&orderby=date');

	while (have_posts()):
		the_post();
?>
			<li>
				<a href="<?php the_permalink(); ?>" class="fancy-thumb">
					<div class="caption">
<?php
		if (has_post_thumbnail()):
?>
						<?php the_post_thumbnail('thumbnail', 'title='); echo "\n"; ?>
<?php
		else:
?>
						<img src="http://cambelt.co/243x172/No Image?color=888888,ffffff" alt="" />
<?php
		endif;
?>
						<h4 class="caption-title"><?php the_title(); ?></h4>
					</div>
				</a>
			</li>
<?php
	endwhile;
	wp_reset_query();
?>
		</ul>
	<!--/ .content --></section>
	<section class="content content-ground">
		<h3 class="ribbon"><a href="http://twitter.com/builtlast" target="_blank">Tweets</a></h3>
		<ul id="twitter-status" class="compact"></ul>
	<!--/ .content --></section>
<!--/ .container --></div>
<?php get_footer(); ?>