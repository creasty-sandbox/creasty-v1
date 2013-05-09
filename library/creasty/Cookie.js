
creasty.create("creasty.Cookie", {
	allowed : function(){
		return navigator.cookieEnabled;
	},
	/*---------------------------------------
	@Parameters:
	name - string
	value - string
	duration - number (of days)
	path - string
	domain - string
	secure - boolean
	---------------------------------------*/
	set : function(name, value, options){
		cookie = [];
		cookie.push(name + "=" + escape(value));
		options = options || {};
		if(options.duration){
			date = new Date();
			date.setTime(date.getTime() + (options.duration * 86400000));
			cookie.push("expires=" + date.toGMTString());
		}
		if(options.path){
			cookie.push("path=" + options.path || "/");
		}
		if(options.domain){
			cookie.push("domain=" + options.domain);
		}
		if(options.secure){
			cookie.push("secure");
		}
		document.cookie = cookie.join(";");
	},
	/*---------------------------------------
	@Parameters:
	name - string
	---------------------------------------*/
	get : function(name){
		var result, test;
		rexp=new RegExp(name + "=(.*)");
		document.cookie.split(";").detect(function(cookie){
			if((test = cookie.match(rexp))){
				result = unescape(decodeURI(test[1]));
			}
			return test;
		});
		return result;
	},
	/*---------------------------------------
	@Parameters:
	name - string
	---------------------------------------*/
	remove : function(name){
		this.set(name, "", { duration : "-1" });
	}
});
