
/*
<div class="dyn-collapse">
	<h2 class="collapse-title">Panel Title 1</h2>
	<div class="collapse-content">
		Panel Content 1
	</div>
	<h2 class="collapse-title">Panel Title 2</h2>
	<div class="collapse-content">
		Panel Content 2
	</div>
</div>
 */

require("creasty.Navigator").done(function(){
		
	creasty.create("creasty.Collapse", function(o, multi){
		if(!o.dynTitle || !o.content || !o.menu) return;
		
		var core = new creasty.Navigator(o.content, o.menu, {
			viewstates : {},
			init : function(){},
			isOpens : function(id){
				return this.viewstates[id];
			},
			addView:function(id){
				this.viewstates[id] = true;
			},
			removeView:function(id){
				this.viewstates[id] = false;
			}
		});
		
		core.viewstate = -1;
		
		var basecnt = core.content.eq(0),
			padtop = basecnt.css("paddingTop"),
			padbottom = basecnt.css("paddingBottom");
		
		core.hideMethod = function(id){
			var ele = core.getContentNode(id);
			ele.show().animate({
				height : "hide",
				paddingTop : 0,
				paddingBottom : 0,
				opacity : "hide"
			},{
				duration : 300,
				easing : "easeOutCubic"
			});
		};
		core.showMethod = function(id){
			var ele = core.getContentNode(id);
			ele.hide().animate({
				height : "show",
				paddingTop : padtop,
				paddingBottom : padbottom,
				opacity : "show"
			},{
				duration : 300,
				easing : "easeOutCubic"
			});
		};
		
		if(multi == true){
			core.init(core.totalstate);
			
			core.setCurrentView = function(viewId){
				if(core.isOpens(viewId)){
					core.getNavNode(viewId).removeClass("active");
					core.getContentNode(viewId).removeClass("show");
					core.hideMethod(viewId);
					core.removeView(viewId);
				}else{
					core.getNavNode(viewId).addClass("active");
					core.getContentNode(viewId).addClass("show");
					core.showMethod(viewId);
					core.addView(viewId);
				}
			};
		}
		
		if($.isNumeric(o.defaultPanel)){
			if(multi == true){
				core.addView(o.defaultPanel);
			}else{
				core.setCurrentView(o.defaultPanel);
			}
			
			core.nav.eq(o.defaultPanel).addClass("active");
			core.content.eq(o.defaultPanel).addClass("show");
		}
		
		core.initController = function(evnt){
			core.nav.bind(evnt, function(e){
				var pos = core.getControllerId(this, o.dynTitle);
				core.setCurrentView(pos);
				core.stopAdvance();
				e.preventDefault();
			});
		};
		
		var openEvent = o.onhover ? "mouseover" : "click";
		core.initController(openEvent);
	});
	
	creasty.create("creasty.module.Collapse", function(){
		var DYN_ROOT = ".dyn-collapse",
			DYN_CONTENT = ".collapse-content",
			DYN_TITLE = ".collapse-title",
			DYN_SET_DEFAULT = ".collapse-default",
			OPTION_MULTI = "collapse-multi",
			OPTION_ONHOVER = "collapse-hover";
		
		$(DYN_ROOT).each(function(){
			var $target = $(this),
				title = $target.find(DYN_TITLE),
				content = $target.find(DYN_CONTENT),
				defaultpanel = $target.find(DYN_SET_DEFAULT);
			
			var multi = $target.hasClass(OPTION_MULTI),
				open = (defaultpanel.length != 0) ? defaultpanel.prevAll(DYN_CONTENT).length : false,
				onhover = (!multi && $target.hasClass(OPTION_ONHOVER)) ? true : false;
			
			creasty.Collapse({
				dynTitle : DYN_TITLE,
				content : content,
				menu : title,
				defaultPanel : open,
				onhover : onhover
			}, multi);
		});
	});
	
	require.ready(function(){
		creasty.module.Collapse();
	});

});
