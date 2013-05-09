<?php get_header(); ?>
<div class="container">
	<div class="content content-ground">
		<i class="icon-alert"></i>
		<p class="lead txt-center">アクセスしようとしたページが見つかりません。
		<br />ご覧になっていたページからのリンクが無効になっているか、あるいはアドレスのタイプミスかもしれません。</p>
		<p class="lead txt-center"><a href="/" class="icon icon-home">ホーム</a>に戻る。</p>
		<br />
		<aside>
			<h3 class="ribbon"><span>サイト内を検索する</span></h3>
			<form action="<?php esc_attr_e(home_url('/')); ?>" method="get" class="form-inline txt-center">
				<input type="text" name="s" class="text span-7" placeholder="キーワード" />
				<button type="submit" class="btn">検索</button>
			</form>
			<script>require('form');</script>
		</aside>
	<!--/ .content --></div>
<!--/ .container --></div>
<?php get_footer(); ?>