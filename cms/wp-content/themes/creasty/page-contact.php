<?php

define('CAPTCHA_SCRIPT', '/library/php/Form/captcha/get.php');

require_once(WWWROOT . '/library/php/Form/Form.class.php');

$cf = new Form(array(
	'prefix' => 'cf',
	'mail'   => true,
	'nonce'  => '8F69rGWR6sGWzi33',
));

$cf->add('name', true)->minlen(3)->maxlen(12);
$cf->add('email', true)->type('email');
$cf->add('type')->set_option(array(
	'無料見積もり',
	'お問い合わせ',
));
$cf->add('tel')->type('tel');
$cf->add('services+')->set_option(array(
	'新しくホームページを立ち上げたい',
	'今あるホームページをリニューアルしたい',
	'ホームページにブログを設置したい',
	'オリジナルのデザインでブログを運営したい',
	'更新が簡単なホームページにしたい',
	'スマートフォンでも見れるようにしたい',
	'コーディング業務だけ委託したい',
	'ロゴを作って欲しい',
));
$cf->add('message')->maxlen(1000);
$cf->add('deadline', true)->set_option(array(
	'1ヶ月〜2ヶ月',
	'2ヶ月〜4ヶ月',
	'4ヶ月〜6ヶ月',
	'時間は関係ない',
));
$cf->add('message', true)->maxlen(1000);
$cf->add_captcha();

$cf->submit(array(
	'from' => 'クリスティお問い合わせ <contact@creasty.com>',
	'cc' => 'contact@creasty.com',
	'to' => 'email',
	'subject' => 'お問い合わせありがとうございます',
	'body' => '
このメールはお問い合わせフォームから自動で送信されました。
お問い合わせ日時: {{DATE}} {{TIME}}

----------------------------------------
お名前:
{{name}}
----------------------------------------
メールアドレス:
{{email}}
----------------------------------------
電話番号:
{{tel}}
----------------------------------------
サービス:
{{services+}}
----------------------------------------
プロジェクトの内容:
{{message}}
----------------------------------------
希望納期:
{{deadline}}
----------------------------------------
',
));


get_header();

?>
<div class="container">
	<div class="content content-ground" role="main">
		<p class="alert alert-info txt-big">只今、新規の制作を受け付けておりません。</p>
		<br />

		<form id="cf" action="<?php the_permalink(); ?>" method="post">
			<h3><label class="icon-user" for="cf-name">お名前 <small class="required">(必須)</small></label></h3>
			<div class="col-ab-a">
				<p><?php $cf->html->text('name', array( 'class' => 'text span-12' )); ?></p>
	 		</div>
	 		<div class="col-ab-b">
				<p>私はクレイジーな人間ですが、安心して下さい、噛み付いたりしませんから(笑)</p>
	 		</div>
	 		<br class="clear" />

			<h3><label class="icon-mail" for="cf-email">メールアドレス <span class="required">(必須)</span></label></h3>
			<div class="col-ab-a">
				<p><?php $cf->html->text('email', array( 'class' => 'text span-12' )); ?></p>
	 		</div>
	 		<div class="col-ab-b">
	 			<p>今後の連絡で使用しますのでよくチェックするメールアドレスを入力してください。お問い合わせ送信後、自動返信メールが届きます。<i class="icon-help tooltip" title="携帯のメールアドレスを使用される方は、事前に「creasty.com」のドメインを受信できるようにしてください。"></i></p>
	 		</div>
	 		<br class="clear" />

			<h3><label class="icon-call" for="cf-tel">電話番号</label></h3>
			<div class="col-ab-a">
				<p><?php $cf->html->text('tel', array( 'class' => 'text span-12' )); ?></p>
	 		</div>
	 		<div class="col-ab-b">
		 		<p>お問い合わせの後、詳しいことをヒアリングさせていただきます。お電話でのヒアリングを希望される方はご入力ください。</p>
	 		</div>
	 		<br class="clear" />

	 		<hr />

		 	<h3 class="icon-gear">どのような形でお役に立てますか？</h3>
	 		<div class="col-ab-a">
		 		<ul id="cf-services" class="link-list">
		 			<?php $cf->html->option('services+'); ?>
		 		</ul>
	 		</div>
			<div class="col-ab-b">
				<p>何でもかんでもやっているわけではないので・・・
				<br />カッコいい“椅子”を作って欲しいと言われても困ります。</p>
			</div>
			<br class="clear" />

			<h3><label class="icon-compass" for="cf-desc">プロジェクトの内容を簡単に教えて下さい <span class="required">(必須)</span></label></h3>
			<div class="col-ab-a">
				<?php $cf->html->textarea('message', array( 'class' => 'span-12', 'rows' => '15' )); ?>
			</div>
			<div class="col-ab-b">
				<p>対応を円滑にすすめるために概要をざっくりとお聞かせ下さい。
				<br />例えば以下のようなことを書いていただけると助かります。</p>
				<ul>
					<li>何を目的としているか</li>
					<li>ターゲットは誰なのか</li>
					<li>どんな競合相手がいるか</li>
					<li>必要な機能はどういうものか</li>
				</ul>
			</div>
			<br class="clear" />

	 		<h3 class="icon-calender">納期はいつまででしょうか？ <span class="required">(必須)</span></h3>
	 		<div class="col-ab-a">
		 		<ul id="cf-deadline" class="link-list">
			 		<?php $cf->html->option('deadline'); ?>
		 		</ul>
	 		</div>
	 		<div class="col-ab-b">
				<p>ここでの納期というのは、正式な受注(契約)をもってからの期間です。また契約後、お客様にサイトのコンテンツを用意していただきますが、その間は製作時間に含めませんのでご了承ください。</p>
				<p>詳しくは、<a href="/service#workflow">こちら</a>をご覧ください。</p>
	 		</div>
			<br class="clear" />
			<hr />

			<h3><label class="icon-picture" for="cf-captcha">画像認証 <span class="required">(必須)</span></label></h3>
			<div class="col-ab-a">
				<p><?php $cf->html->captcha_image(array( 'class' => 'thumb', 'title' => '読みにくいですか？&lt;br /&gt;クリックすると別の文字を表示します' )); ?></p>
				<p><?php $cf->html->captcha(array( 'class' => 'text span-12' )); ?></p>
			</div>
			<div class="col-ab-b">
				<p>スパム対策です。
				<br />お手数ですが画像に表示されている文字を入力してください。</p>
			</div>
			<br class="clear" />

			<hr />

			<div class="col-ab-a">
				<?php $cf->html->nonce(); ?>
				<button type="submit" class="btn btn-blue btn-large">送信</button>
			</div>
			<div class="col-ab-b">
				<p>ご入力いただきました個人情報は、お問合わせの目的で以外で無断で利用したり、第三者へ開示したりすることはありませんのでご安心ください。</p>
			</div>
			<br class="clear" />
		</form>
		<div id="thank-you" class="hide">
			<h3>Thank You So Much</h3>
			<p class="lead txt-center">お問い合せありがとうございました。
			<br />ご入力いただきましたメールアドレス宛に自動返信メールが届きますのでご確認ください。
			<br />追ってご連絡致します。</p>
			<p><br /></p>
		</div>
		<script>
			require('form', 'validator', 'tooltip', '!ready').done(function () {
				$('#cf').FormValidator({
					afterSetup: function ($form, f) {
						f.captchaImage.powerTip({
							placement: 'e',
							mouseOnToPopup: true,
							smartPlacement: true
						});
					},
					success: function ($form) {
						$form.animate({
							height: 'hide'
						}, {
							easing: 'easeInOutQuad',
							duration: 1000,
							complete: function () {
								$('#thank-you')
								.removeClass('hide')
								.hide()
								.animate({
									height: 'show'
								}, {
									easing: 'easeInOutQuad',
									duration: 600
								});
							}
						});
					}
				});
			});
		</script>
	<!--/ .content --></div>
<!--/ .container --></div>
<?php get_footer(); ?>