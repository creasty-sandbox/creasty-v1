/*!
 * Flipper JS
 *
 * @author ykiwng
 */

(function($){
	var flipper = function($container){
		var $panel_front = $container.find('.flipper-front'),
			$panel_back = $container.find('.flipper-back');
		
		var height,
		resize = function(){
			// reset fixed height from the container so that the panel can stretch
			$container.height('auto');
			
			height = $panel_front.height();
			
			$panel_back.height(height);
			$container.height(height);
		};
		
		$(window).resize(resize);
		resize();
		
		var func = function(){
			$container.toggleClass('flipped');
			$.support.css3d || $panel_front.toggle();
		};
		
		$container.addClass('init').bind({
			hover: func,
			touchstart: function(e){
				var touch = e.originalEvent.touches[0];
				this.start = touch.pageX;
			},
			touchmove: function(e){
				var touch = e.originalEvent.touches[0];
				this.diff = touch.pageX - this.start;
			},
			touchend: function(){
				var threshold = dim.width * 0.4;
				
				if(this.diff > threshold){ // left
					func();
				}else if(this.diff < -threshold){ // right
					func();
				}
			}
		});
	};
	
	$.support.css3d = (function(){
		var props = ['perspectiveProperty', 'WebkitPerspective', 'MozPerspective'],
			testDom = document.createElement('a');
		
		for(var i = 0, len = props.length; i < len; i++){
			if(props[i] in testDom.style){
				return true;
			}
		}
		
		return false;
	})();
	
	$.fn.flipper = function(){
		return this.each(function(){
			flipper($(this));
		});
	};
})(jQuery);
