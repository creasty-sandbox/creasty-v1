/*!
 * Tap Event
 * 
 * @author ykiwng
 */

(function(document, $){
	$.event.special.tap = {
		setup: function(a, b){
			var $this = $(this);
			
			if(window.Touch){
				$this.bind('touchstart', $.event.special.tap.onTouchStart);
				$this.bind('touchmove', $.event.special.tap.onTouchMove);
				$this.bind('touchend', $.event.special.tap.onTouchEnd);
			}else{
				//$this.bind('click', $.event.special.tap.click);
			}
		},
		click: function(a){
			a.type = 'tap';
			jQuery.event.handle.apply(this, arguments);
		},
		teardown: function(a){
			var $this = $(this);
			
			if(window.Touch){
				$this.unbind('touchstart', $.event.special.tap.onTouchStart);
				$this.unbind('touchmove', $.event.special.tap.onTouchMove);
				$this.unbind('touchend', $.event.special.tap.onTouchEnd);
			}else{
				//$this.unbind('click', $.event.special.tap.click);
			}
		},
		onTouchStart: function(a){
			this.moved = false;
		},
		onTouchMove: function(a){
			this.moved = true;
		},
		onTouchEnd: function(a){
			if(!this.moved){
				a.type = 'tap';
				$.event.handle.apply(this, arguments);
			}
		}
	};
	
	// simulate hover
	$(document).ready(function(){
		$('a, input[type="button"], input[type="submit"], button')
		.bind('touchstart, touchend', $.noop);
	});

})(document, jQuery);
