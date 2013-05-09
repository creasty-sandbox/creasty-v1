
/*

.dyn-promo.promo-autoplay>.promo-slide*


<div class="dyn-promo promo-autoplay">
	<div class="promo-slide">
		
	</div>
	<div class="promo-slide">
		
	</div>
</div>

-----------------------------------------------

<div class="dyn-promo promo-autoplay">
	<div class="promo-content">
		<div class="promo-slide">
			
		</div>
		<div class="promo-slide">
			
		</div>
	</div>
	<div class="promo-indicator">
		<span></span>
		<span></span>
	</div>
	<span class="carousel-back"></span>
	<span class="carousel-next"></span>
</div>

*/

require("creasty.Navigator").done(function(){
	
	creasty.create("creasty.Promo", function(o){
		if(!isset(o.content, o.indicator)) return;
		
		var core = new creasty.Navigator(o.content, o.indicator),
			maskwidth = core.content.eq(0).width();
		
		core.content.parent().width(maskwidth * core.content.length);
		
		core.showMethod = function(){
			core.content.stop().animate({
				left : -maskwidth * core.viewstate + "px"
			},{
				duration : 500,
				easing : "easeOutCubic"
			});
		};
		
		if(isset(o.back, o.next)){
			o.back.click(function(e){
				e.preventDefault();
				core.stopAdvance();
				core.prev();
			});
			o.next.click(function(e){
				e.preventDefault();
				core.stopAdvance();
				core.next();
			});
		}
		core.initController();
		core.initContent();
		
		isset(o.autoSliding) && core.autoAdvance(o.autoSliding);
	});
	
	creasty.create("creasty.module.Promo", function(){
		var DYN_ROOT = ".dyn-promo",
			DYN_SLIDE = ".promo-slide",
			OPTION_AUTOPLAY = "promo-autoplay",
			CREATE_CONTENT = "promo-content",
			CREATE_INDICATOR = "promo-indicator",
			CREATE_BACK = "promo-back",
			CREATE_NEXT = "promo-next";
		
		$(DYN_ROOT).each(function(){
			var $this = $(this),
				wrap = $this.wrapInner($('<div/>').attr("class", CREATE_CONTENT)),
				slide = wrap.find(DYN_SLIDE);
			
			if(!slide || slide.length <= 1)return;
			
			slide.width($this.width());
			slide.height($this.height());
			
			var indicator = $('<div/>').attr("class", CREATE_INDICATOR).append(
				$('<span/>').repeat(slide.length)
			).appendTo(wrap).children();
			
			var back = $('<div/>').attr("class", CREATE_BACK).appendTo(wrap),
				next = $('<div/>').attr("class", CREATE_NEXT).appendTo(wrap),
				autoplay = $this.hasClass(OPTION_AUTOPLAY) ? 7 : null;
			
			creasty.Promo({
				content: slide,
				indicator: indicator,
				back: $(back),
				next: $(next),
				autoSliding: autoplay
			});
		});
	});
	
	require.ready(function(){
	//	creasty.module.Promo();
	});
	
});

