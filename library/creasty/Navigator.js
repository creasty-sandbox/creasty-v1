
creasty.create("creasty.Navigator", (function(){
	function fixViewId(c, viewId){
		if(viewId != null && $.isNumeric(viewId)){
			// Fix for negative numbers
			return (c.totalstate + viewId) % c.totalstate;
		}
		return c.viewstate;
	}
	function changeState(c, viewId){
		c.viewstate = fixViewId(c, viewId);
	}
	function isCurrentView(c, viewId){
		return (c.viewstate == fixViewId(c, viewId)) ? true : false;
	}
	
	function getNavNode(c, viewId){
		return c.nav.eq(fixViewId(c, viewId));
	}
	function getContentNode(c, viewId){
		return c.content.eq(fixViewId(c, viewId));
	}
	function getControllerId(ele, selector){
		return $(ele).prevAll(selector).length;
	}
	
	function setCurrentView(c, viewId){
		if(isCurrentView(c, viewId))return;
		
		if(c.viewstate != -1){
			c.nav && getNavNode(c).removeClass("active");
			getContentNode(c).removeClass("show");
			c.hideMethod();
		}
		
		changeState(c, viewId); // Change State
		
		c.nav && getNavNode(c).addClass("active");
		getContentNode(c).addClass("show");
		c.showMethod();
	}
	function prev(c){
		var pos = c.viewstate - 1;
		c.setCurrentView(pos);
	}
	function next(c){
		var pos = c.viewstate + 1;
		c.setCurrentView(pos);
	}
	
	function autoAdvance(c, sec){
		if($.isNumeric(sec)){
			c.autotimer = setInterval(function(){
				c.next();
			}, sec * 1000);
		}
	}
	function stopAdvance(c){
		clearInterval(c.autotimer);
	}
	
	function initController(c, evnt){
		c.nav.bind(evnt, function(e){
			var pos = getControllerId(this);
			c.setCurrentView(pos);
			stopAdvance(c);
			e.preventDefault();
		});
		
		// Mark the "current"-th controller as active
		c.nav.eq(c.viewstate).addClass("active");
	}
	function initContent(c){
		// Mark the "current"-th content as show
		c.content.eq(c.viewstate).addClass("show");
	}
	
	var c0nstruct0r = function(content, nav, ext){
		if(content == null) return;
		this.content = $(content);
		
		if(nav){
			this.nav = $(nav);
			this.totalstate = this.nav.length;
		}else{
			this.totalstate = this.content.length;
		}
		this.viewstate = 0;
		this.autotimer = null;
		
		if($.isPlainObject(ext)){
			var $this = this;
			$.extend($this, ext);
		}
	};
	$.extend(c0nstruct0r.prototype, {
		showMethod : function(){},
		hideMethod : function(){},
		
		isCurrentView : function(viewId){
			return isCurrentView(this, viewId);
		},
		setCurrentView : function(viewId){
			return setCurrentView(this, viewId);
		},
		changeState : function(viewId){
			changeState(this, viewId);
		},
		prev : function(){
			prev(this);
		},
		next : function(){
			next(this);
		},
		
		getControllerId : getControllerId,
		getNavNode : function(viewId){
			return getNavNode(this, viewId);
		},
		getContentNode : function(viewId){
			return getContentNode(this, viewId);
		},
		
		autoAdvance : function(sec){
			autoAdvance(this, sec);
		},
		stopAdvance : function(){
			stopAdvance(this);
		},
		
		initController : function(evnt){
			evnt = evnt || "click";
			initController(this, evnt);
		},
		initContent : function(){
			initContent(this);
		}
	});
	return c0nstruct0r;
})());
