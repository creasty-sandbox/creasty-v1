<!doctype html>
<!--
	      g_
	    g@@@@_
	 _p_`#@@@@@m_
	'#@@m_ #@@@@@L
	g_`#* __ *@@@|        _gg@Mp     ggggmm_    qggggggp      ggm       _g@Mm_   gggggggp  gm     gg
	## _ #@@@_ *@|       g@#*"*#@,   @@***#@L   {@F*****      @#@,     #@*"*#@   ***@#***  '@h   g@"
	"_g@@_ *@@@m_`      q@F     "    @@    @@   {@           g@ @@     @@           @F      `@L _@F
	#@@@@@@_ *@@@p      #@           @@   _@#   {@mgggg,     @F d@L    #@@Mp_       @F       \@m@F
	#@@@@@@@@m_"#|      @@           @@@@@@#    {@#####+    #@   #@      *#@@@,     @F        #@F
	#@@@@@@@@@@m_       #@_     _    @@  #@     {@         _@@ggg@@L         @@     @F         @L
	#@@@@@@@@@@@@L       #@m___g@F   @@   @@    {@______   #@*****@@   @m___g@F     @F         @L
	#@@@@@@@@@@@@|        "#@@@#"    @@   `@h   {@@@@@@F  q@F     \@L  "#@@@@*      @F         @L
	#@@@@@@@@@@@@|
	 *@@@@@@@@@#*
	   "#@@@@#"
	     `##
-->
<html lang="ja" prefix="og: http://ogp.me/ns# fb: http://www.facebook.com/2008/fbml">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0" />
	<title><?php tpl_param('pagetitle', ''); ?></title>
	<link rel="alternate" href="<?php bloginfo('rss2_url'); ?>" type="application/rss+xml" title="Creasty ブログ" />
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,700" />
	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/master.css<?php echo get_file_ver(TEMPLATEPATH . '/master.css'); ?>" />
	<script src="/library/scripts.js"></script>
	<script src="/library/creasty.js<?php echo get_file_ver(WWWROOT . '/library/creasty.js'); ?>"></script>
<?php
	wp_head();
?>
</head>
<body <?php crst::body_id(); ?> <?php crst::body_class(); ?>>
<div id="band">
	<header id="globalheader" class="container">
		<h1 id="creasty-logo"><a href="/" title="ホーム">Creasty</a></h1>
		<nav>
			<ul id="gnav-site-menu" class="menu compact">
<?php
	foreach (crst::nav_menu('global-header') as $menu):
		$options = '';

		if(!empty($menu['classes']))
			$options .= ' class="' . $menu['classes'] . '"';

		if(!empty($menu['attr_title']))
			$options .= ' title="' . $menu['attr_title'] . '"';

		if(!empty($menu['target']))
			$options .= ' target="' . $menu['target'] . '"';
?>
				<li><a href="<?php echo esc_url($menu['link']); ?>"<?php echo $options; ?>><?php echo esc_html($menu['title']); ?></a></li>
<?php
	endforeach;
?>
			</ul>
		</nav>
	</header>
<?php
	if (get_tpl_param('doctitle') && get_tpl_param('template') != 'portal'):
?>
	<header id="document" class="container">
		<ol id="breadcrumb" class="menu compact">
<?php
			foreach (Breadcrumb::get_array() as $bc):
?>
			<li><a href="<?php echo relative_url($bc['link']); ?>"><?php echo esc_html($bc['title']); ?></a></li>
<?php
			endforeach;
?>
		</ol>
		<h2><?php tpl_param('doctitle', ''); ?></h2>
	</header>
<?php
	endif;
?>
</div>
