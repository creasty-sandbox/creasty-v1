
/*
<div class="dyn-carousel">
	<div class="carousel-content"></div>
</div>

----------------------------------------------

<div class="dyn-carousel">
	<div class="carousel-wrap">
		<div class="carousel-content"></div>
	</div>
	<span class="carousel-back"></span>
	<span class="carousel-next"></span>
</div>

*/

require("creasty.Navigator").done(function(){
	
	creasty.create("creasty.Carousel", function(o){
		var core = new creasty.Navigator(o.content, null, {
			position : 0,
			max : 1,
			shift : 0,
			init : function(c, m){
				this.max = Math.ceil(c / m);
				this.shift = (c - m) / (this.max - 1);
			},
			posfix : function(pos){
				return (pos + this.max) % this.max;
			},
			setPosition : function(pos){
				this.position = this.posfix(pos);
			},
			isPosition : function(pos){
				return this.position == this.posfix(pos);
			},
			backPosition : function(){
				this.setPosition(this.position - 1);
			},
			nextPosition:function(){
				this.setPosition(this.position + 1);
			}
		});
		
		var contentWidth = 0;
		core.content.each(function(ele){
			contentWidth += $(this).outerWidth(true);
		});
		
		core.content.parent().width(contentWidth);
		
		core.init(contentWidth, o.root.width());
	
		core.showMethod = function(){
			core.content.stop().animate({
				left: -core.position * core.shift + "px"
			}, 400);
		};
		
		o.back.click(function(e){
			e.preventDefault();
			core.backPosition();
			core.showMethod();
		});
		o.next.click(function(e){
			e.preventDefault();
			core.nextPosition();
			core.showMethod();
		});
		
		core.initContent();
	});
	
	creasty.create("creasty.module.Carousel", function(){
		var DYN_ROOT = ".dyn-carousel",
			DYN_CONTENT = ".carousel-content",
			CREATE_WRAP1 = "carousel-wrap",
			CREATE_WRAP2 = "carousel-wrap2",
			CREATE_BACK = "carousel-back",
			CREATE_NEXT = "carousel-next";
		
		$(DYN_ROOT).each(function(ele){
			var $target = $(this);
			
			var content = $target.find(DYN_CONTENT).wrapAll($('<div/>').attr("class", CREATE_WRAP2)).wrapAll($('<div/>').attr("class", CREATE_WRAP1));
			
			var back = $('<div/>').attr("class", CREATE_BACK).appendTo($target);
			var next = $('<div/>').attr("class", CREATE_NEXT).appendTo($target);
			
			creasty.Carousel({
				root : $target,
				content : content,
				back : $(back),
				next : $(next)
			});
		});
	});
	
	require.ready(function(){
		creasty.module.Carousel();
	});

});



