
/** 
 * State Manager
 */

require("creasty.Cookie").done(function(){

	creasty.create("creasty.StateManager", (function(){
		var loadedUri,
			loadedCookies = {},
			cookiesDays = {},
			cookies = creasty.Cookie;
			
		function getUri(){
			return loadedUri || (loadedUri = window.location.search.toQueryParams());
		}
		function getUriId(id){
			return getUri()[id];
		}
		function getSaveDays(ns){
			return cookiesDays[ns] || 0;
		}
		function setSaveDays(ns, days){
			cookiesDays[ns] = days;
		}
		function getSave(ns){
			return loadedCookies[ns] || (loadedCookies[ns] = (cookies.get(ns) || "").toQueryParams());
		}
		function clearSave(ns, id){
			save = getSave(ns);
			delete save[id];
			val = $H(save).toQueryString();				
			return val ? cookies.set(ns, val, { duration : getSaveDays(ns) }) : cookies.remove(ns);
		}
		function setSave(ns, id, val){
			var itemsToSave;
			if(isArray(val)){
				itemsToSave = val.join(",");
			}else if(isObject(val)){
				itemsToSave = $H(val).inject([], function(arr, state){
					value = state.value || 0;
					if(value && isBoolean(value)) value = 1;
					arr.push(state.key + ":" + value);
					return arr;
				}).join(",");
			}else if(isString(val)){
				itemsToSave = val;
			}else if(isBoolean(val)){
				itemsToSave = val ? "1" : "0";
			}else if(isNumber(val)){
				itemsToSave = val.toString();
			}
			if(!itemsToSave) return clearSave(ns, id);
			getSave(ns)[id] = itemsToSave;
			cookies.set(ns, $H(getSave(ns)).toQueryString(), { duration : getSaveDays(ns) });
		}
		c0nstruct0r = function(nam3spac3, days2save){
			this.ns = nam3spac3;
			setSaveDays(nam3spac3, days2save || 0);
			return this;
		};
		c0nstruct0r.prototype = {
			setCookieParam : function(id, state){
				setSave(this.ns, id, state);
			},
			getCookieParam : function(id){
				return getSave(this.ns)[id];
			},
			removeCookieParam : function(id){
				return clearSave(this.ns, id);
			},
			getQueryParam : function(id){
				return id ? getUriId(id) : getUri();
			}
		};
		return c0nstruct0r;
	})());
	
});
