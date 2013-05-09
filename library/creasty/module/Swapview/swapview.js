
/*
<div class="dyn-swapview">
	<div class="swapview-content"></div>
	<ul class="swapview-nav">
		<li></li>
	</ul>
</div>

<div class="dyn-swapview">
	<div class="swapview-content"></div>
</div>

*/

require("creasty.Navigator").done(function(){
	
	creasty.create("creasty.Swapview", function(o){
		if(!o.content) return;
		
		var core = new creasty.Navigator(o.content, o.nav);
		
		core.showMethod = function(){
			core.getContentNode().css("position", "absolute").animate({ opacity : "show" }, 700);
		};
		core.hideMethod=function(){
			core.getContentNode().css("position", "absolute").animate({ opacity : "hide" }, 700);
		};
		
		o.nav
		? core.initController()
		: core.autoAdvance(8);
		
		core.initContent();
		core.getContentNode().css("position", "absolute").show();
	});
	
	creasty.create("creasty.module.Swapview", function(){
		var DYN_ROOT = ".dyn-swapview",
			DYN_CONTENT = ".swapview-content",
			DYN_NAV = ".swapview-nav>li",
			CREATE_WRAP = "swapview-wrap";
		
		$(DYN_ROOT).each(function(ele){
			var $target = $(this),
				content = $target.find(DYN_CONTENT).wrapAll($('<div/>').attr("class", CREATE_WRAP)),
				nav = $target.find(DYN_NAV);
			
			if(nav.length == 0) nav = null;
			
			var maxHeight = 0;
			content.each(function(){
				var h = $(this).outerHeight();
				maxHeight = (h > maxHeight) ? h : maxHeight;
			});
			
			$target.find("." + CREATE_WRAP).height(maxHeight);
			
			creasty.Swapview({
				content : content,
				nav : nav
			});
		});
	});
	
	require.ready(function(){
		creasty.module.Swapview();
	});
	
});
