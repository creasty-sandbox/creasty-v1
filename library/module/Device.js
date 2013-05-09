/*!
 * Device Detection
 * 
 * @author ykiwng 
 */


var Device = (function(){
	var device = {}, os, browser,
		ua = navigator.userAgent.toLowerCase(),
		up = navigator.platform.toLowerCase();
	
	os =
		/(win)/.exec(up) ||
		/(mac) os/.exec(up) ||
		/(mac)intel/.exec(up) ||
		/(linux)/.exec(up) ||
		/(iphone) os/.exec(ua) ||
		/(ipad)/.exec(ua) ||
		/(ipod)/.exec(ua) ||
		/(android)/.exec(ua) ||
		[];
	browser =
		/(firefox)[\s\/]([\w.]+)/.exec(ua) ||
		/()version[\s\/]([\w.]+)[\s\/](safari)/.exec(ua) ||
		/(chrome)[\s\/]([\w.]+)/.exec(ua) ||
		/(opera)(?:.*version)?[\s\/]([\w.]+)/.exec(ua) ||
		/ms(ie) ([\w.]+)/.exec(ua) ||
		/(webkit)[\s\/]([\w.]+)/.exec(ua) ||
		/(mozilla)(?:.*? rv:([\w.]+))?/.exec(ua) && ua.indexOf("compatible") == -1 ||
		[];
	
	device.os = os[1] || "unknown";
	device.browser = browser[1] || browser[3] || "unknown";
	device.version_raw = browser[2] || "0";
	device.version = parseFloat(device.version_raw, 10);
	
	device[device.os] = true;
	device.desktop = device.win || device.mac || device.linux;
	device.ios = device.iphone || device.ipad || device.ipod;
	device.mobile = device.android || device.ios;
	
	device[device.browser] = true;
	device.webkit = device.webkit || device.safari || device.chrome;
	device.mozilla = device.mozilla || device.firefox;
	
	return device;
})();
