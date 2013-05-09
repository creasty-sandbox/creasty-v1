/** 
 * Owl Notifications
 * 
 * @ver 3.0
 * @url http://std.li/07
 * @author moveteam
 * @modified ykiwng
 * 		- Changed the apperance for text-only notifications
 * 		- Japaniese localization
 */

(function ($) {
    $.notification = function (settings) {
       	var con, notification, dismiss, image, right, left, inner;
        
        settings = $.extend({
        	title: undefined,
        	content: undefined,
        	timeout: 0,
        	img: undefined,
        	border: true,
        	fill: false,
        	showTime: false,
        	click: undefined,
        	icon: undefined,
        	color: undefined,
        	error: false
        }, settings);
        
        con = $("#notifications");
        if (!con.length) {
            con = $("<div>", { id: "notifications" }).appendTo( $("body") );
        };
        
		notification = $("<div>");
        notification.addClass("notification animated fadeInLeftMiddle fast");
        
        if( settings.icon || settings.error || settings.img ){
	        con.addClass( 'hasIcons' );
        }else{
	        notification.addClass( 'textOnly' );
        }
        
        if(settings.error == true) {
        	notification.addClass("error");
        }
        
        if( $("#notifications .notification").length > 0 ) {
        	notification.addClass("more");
        } else {
        	con.addClass("animated flipInX").delay(1000).queue(function(){ 
        	    con.removeClass("animated flipInX");
        			con.clearQueue();
        	});
        }
        
        dismiss = $("<div>", {
			click: function () {
				if($(this).parent().is(':last-child')) {
				    $(this).parent().remove();
				    $('#notifications .notification:last-child').removeClass("more");
				} else {
					$(this).parent().remove();
				}
			}
		});
		
		dismiss.addClass("dismiss");

		left = $("<div class='left'>");
		right = $("<div class='right'>");
		
		if(settings.title != undefined) {
			var htmlTitle = "<h2>" + settings.title + "</h2>";
			notification.addClass("big");
		} else {
			var htmlTitle = "";
		}
		
		if(settings.content != undefined) {
			var htmlContent = settings.content;
		} else {
			var htmlContent = "";
		}
		
		inner = $("<div>", { html: htmlTitle + htmlContent });
		inner.addClass("inner");
		
		inner.appendTo(right);
		
		if (settings.img != undefined) {
			image = $("<div>", {
				style: "background-image: url('"+settings.img+"')"
			});
		
			image.addClass("img");
			image.appendTo(left);
			
			if(settings.border==false) {
				image.addClass("border")
			}
			
			if(settings.fill==true) {
				image.addClass("fill");
			}
			
		} else {
			if (settings.icon != undefined) {
				var iconType = settings.icon;
			} else {
				if(settings.error!=true) {
					var iconType = '"';
				} else {
					var iconType = 'c';
				}
			}	
			icon = $('<div class="icon">').html(iconType);
			
			if (settings.color != undefined) {
				icon.css("color", settings.color);
			}
			
			icon.appendTo(left);
		}

        left.appendTo(notification);
        right.appendTo(notification);
        
        dismiss.appendTo(notification);
        
        function timeSince(time){
        	var time_formats = [
        	  [2, "1秒", 0], // 60*2
        	  [60, "秒", 1], // 60
        	  [120, "1分", 0], // 60*2
        	  [3600, "分", 60], // 60*60, 60
        	  [7200, "1時間", 0], // 60*60*2
        	  [86400, "時間", 3600], // 60*60*24, 60*60
        	  [172800, "1日", 0], // 60*60*24*2
        	  [604800, "日", 86400], // 60*60*24*7, 60*60*24
        	  [1209600, "1週間", 0], // 60*60*24*7*4*2
        	  [2419200, "週間", 604800], // 60*60*24*7*4, 60*60*24*7
        	  [4838400, "1ヶ月", 0], // 60*60*24*7*4*2
        	  [29030400, "ヶ月", 2419200], // 60*60*24*7*4*12, 60*60*24*7*4
        	  [58060800, "1年", 0], // 60*60*24*7*4*12*2
        	  [2903040000, "年", 29030400] // 60*60*24*7*4*12*100, 60*60*24*7*4*12
        	];
        	
        	var seconds = (new Date - time) / 1000;
        	if (seconds < 0)
        		seconds = Math.abs(seconds);
        	
        	var i = 0, format;
        	
        	while (format = time_formats[i++]){
	        	if (seconds < format[0])
	      			return format[2] > 0 ? Math.floor(seconds / format[2]) + format[1] : format[1];
        	}
        	return time;
        };
        
        if(settings.showTime != false) {
        	var timestamp = Number(new Date());
        	
        	timeHTML = $("<div>", { html: "<strong>" + timeSince(timestamp) + "</strong>前" });
        	timeHTML.addClass("time").attr("title", timestamp);
        	timeHTML.appendTo(right);
        	
        	setInterval(
	        	function() {
	        		$(".time").each(function () {
	        			var timing = $(this).attr("title");
	        			$(this).html("<strong>" + timeSince(timing) + "</strong>前");
	        		});
	        	}, 4000)
        	
        }

        notification.hover(
        	function () {
            	dismiss.show();
        	}, 
        	function () {
        		dismiss.hide();
        	}
        );
        
        notification.prependTo(con);
		notification.show();

        if (settings.timeout) {
            setTimeout(function () {
            	var prev = notification.prev();
            	if(prev.hasClass("more")) {
            		if(prev.is(":first-child") || notification.is(":last-child")) {
            			prev.removeClass("more");
            		}
            	}
	        	notification.remove();
            }, settings.timeout)
        }
        
        if (settings.click != undefined) {
        	notification.addClass("click");
            notification.bind("click", function (event) {
            	var target = $(event.target);
                if(!target.is(".dismiss") ) {
                    settings.click.call(this)
                }
            })
        }
        return this
    }
})(jQuery);