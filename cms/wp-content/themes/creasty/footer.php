<footer id="globalfooter">
	<div class="container">
		<ul id="networks" class="clear-after">
			<li><a class="icon-twitter" href="http://twitter.com/<?php echo get_option('twitter'); ?>" target="_blank">Twitter</a></li>
			<li><a class="icon-facebook" href="<?php echo get_option('facebook'); ?>" target="_blank">Facebook</a></li>
			<li><a class="icon-mail" href="mailto:contact@creasty.com">Contact</a></li>
			<li><a class="icon-git" href="http://github.com/creasty" target="_blank">Github</a></li>
			<li><a class="icon-rss" href="http://www.creasty.com/feed" target="_blank">Blog RSS</a></li>
		</ul>
		<p><small>&copy; <?php echo date('Y'); ?> Creasty. All rights reserved. Designed by <a href="/about">Yuki Iwanaga</a>.</small></p>
		<ul id="powered-by" class="menu compact">
			<li data-powertip="WordPress"><a class="wordpress" href="http://wordpress.org/" target="_blank">WordPress</a></li>
			<li data-powertip="jQuery"><a class="jquery" href="http://jquery.com/" target="_blank">jQuery</a></li>
			<li data-powertip="HTML5"><a class="html5" href="http://www.w3.org/html/logo/" target="_blank">HTML5</a></li>
			<li data-powertip="CSS3"><a class="css3">CSS3</a></li>
			<li data-powertip="Responsive Web Design"><a class="responsive">Responsive Web Design</a></li>
			<li data-powertip="Love Apple!"><a class="apple" href="http://www.apple.com/" target="_blank">Love Apple!</a></li>
		</ul>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>