<?php get_header(); ?>
<div class="container">
	<div class="content">
<style type="text/css">
	#container{

		margin: 0;
		padding: 0;
		list-style: none;
	}
	.box {
		background-color: #F0F0F0;
		color: #888;
		font-family: Arial, Tahoma, serif;
		font-size: 13px;
		xpadding: 5px;
		xborder: 3px solid #666;
		float: left;
		position: relative;
		display: block;
	}
	.box p {	
		padding: 10px;
	}
	.box span {
		float: left;
		font-size: 26px;
		font-weight: bold;
	}
	.box.alt {
		background-color: #CCC;
	}

	.grid-col{
		float: left;
	}
	.grid-row{
		zoom: 1;
	}
	.grid-row:after{
		content: "";
		display: block;
		clear: both;
	}
	
	.grid-clear{
		visibility: hidden;
		display: block;
		float: none;
		clear: both;
		margin: 0;
		padding: 0;
		height: 0;
	}
</style>
		<ul id="container">
	        <li class="box"><p><span>01</span>This is box number 1...<br/>dsgad</p></li>
	        <li class="box alt"><p><span>02</span>This is box number 2...</p></li>
	        <li class="box"><p><span>03</span>This is box number 3...</p></li>
	        <li class="box alt"><p><span>04</span>This is box number 4...</p></li>
	        <li class="box"><p><span>05</span>This is box number 5...</p></li>
	        <li class="box alt"><p><span>06</span>This is box number 6...</p></li>
	        <li class="box"><p><span>07</span>This is box number 7...</p></li>
	        <li class="box alt"><p><span>08</span>This is box number 8...</p></li>
	        <li class="box"><p><span>09</span>This is box number 9...</p></li>
        </ul>
		<script>
(function($, window){
	
	$.fn.fluidGrid = function(settings){
		var defaults = {
			columns: 100,
			gutter: 10,
			minWidth: 100
		};
		settings = $.extend(defaults, settings);
		
		var $boxes = $(this),
			$parent = $boxes.parent(),
			clear = '<div class="grid-clear" style="visibility: hidden; display: block; clear: both; margin: 0; padding: 0;" />';
		
		var now;
		
		var layout = function(){
			var col = settings.columns,
				gutter = settings.gutter,
				maxWidth = 0 | $parent.width('100%').width(),
				width;
			
			// bug fix for Firefox / Chrome
			// the width of parent element need to be an integer
			$parent.width(maxWidth);
			
			col = Math.max(1, Math.min(col, 0 | (maxWidth + gutter) / (settings.minWidth + gutter)));
			width = (maxWidth + gutter) / col - gutter;
			
			if(now == col){
				$boxes.width(width);
				return;
			}
			
			// update & reset
			now = col;
			$parent.find('.grid-clear').remove();
			
			var row;
			
			$boxes.each(function(i){
				var $this = $(this).width(width),
					isLast = !!((i + 1) % col == 0);
				
				$this.css({
					'marginRight': (isLast ? 0 : gutter), 
					'marginBottom': gutter 
				});
				
				isLast && $(clear).insertAfter($this);
			});
		};
		
		layout();
		$(window).resize(layout);
		
		return $boxes;
	};
	
})(jQuery, window);

$(function(){
	$('#container > .box').fluidGrid({
		columns: 4,
		gutter: 10,
		minWidth: 200
	});
});
		</script>
	<!--/ .content --></div>
<!--/ .container --></div>
<?php get_footer(); ?>