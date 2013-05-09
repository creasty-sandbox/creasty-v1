
creasty.create("creasty.util", {
	/*////////////////////////////////////////////////////
	@Method: freshenLocation
	
	@Function:
	Create a location string that would force a browser to check cache.
	
	@Parameters:
	uri - string
	param - string [optional]
	
	@Returned Value:
	string
	
	@Usage:
	> creasty.util.freshenLocation("/path" [,"myParam"])
	////////////////////////////////////////////////////*/
	freshenLocation : function(uri, param){
		var query = "?", i = uri.indexOf(query);
		param = param || "time";
		time = query + param + "=" + new Date().getTime();
		
		if(i == -1){
			return (uri + time);
		}else{
			var parts = this.unfreshenLocation(uri, param).split("?");
			return parts.join(time + ((parts[parts.length - 1] == "") ? "" : "&"));
		}
	},
	/*////////////////////////////////////////////////////
	@Method: unfreshenLocation
	
	@Function:
	Remove the query set by <freshenLocation> from a uri string
	
	@Parameters:
	uri - string
	param - string [optional]
	
	@Returned Value:
	string
	
	@Usage:
	> creasty.util.unfreshenLocation("/path" [,"myParam"])
	////////////////////////////////////////////////////*/
	unfreshenLocation : function(uri, param){
		var exp = new RegExp("([\\?&]?)" + (param || "time") + "=\\d*&?", "g");
		return uri.replace(exp, "$1");
	},
	/*////////////////////////////////////////////////////
	@Method: timestamp
	
	@Function:
	Get Unix timestamp for a date
	
	For more information visit 
	"http://www.php.net/manual/en/function.mktime.php"
	
	@Parameters:
	hours - number
	minutes - number
	seconds - number
	month - number
	date - number
	year - number
	
	@Example:
	> creasty.util.timestamp(14,10,2,2,1,2008);
	>> 1201871402
	////////////////////////////////////////////////////*/
	timestamp : function(){
		var d = new Date(), i = 0, len = arguments.length,
		dateManip = [
			function(tt){ return d.setHours(tt) },
			function(tt){ return d.setMinutes(tt) },
			function(tt){ return d.setSeconds(tt) },
			function(tt){ return d.setMonth(tt - 1) },
			function(tt){ return d.setDate(tt) },
			function(tt){ return d.setYear(tt) }
		];
		
		for(; i < len; arg = arguments[i++]){
			if(arg && isNaN(arg)){
				return false;
			}else if(arg){
				if(!dateManip[i](arg)){
					return false;
				}
			}
		}
		return Math.floor(d.getTime() / 1000);
	},
	/*////////////////////////////////////////////////////
	@Method: date
	
	@Function:
	Format a local time/date
	
	@Parameters:
	format* - string
	timestamp - number [optional]
	
	* "o, u, e, I, T, Z, r" are not supported yet
	
	For more information visit 
	"http://www.php.net/manual/en/function.date.php"
	
	@Usage:
	1> creasty.util.date("D, d M Y H:i:s")
	1>> Sun, 23 Mar 2008 12:31:22
	2> creasty.util.date("H:i:s \\i\\s \\G\\M\\T",1206243082)
	2>> 12:31:22 is GMT
	////////////////////////////////////////////////////*/
	date : function(format, timestamp){
		var jsdate = new Date((timestamp || this.timestamp()) * 1000);
		
		var txt_weekdays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
			txt_ordin = { 1 : "st", 2 : "nd", 3 : "rd", 21 : "st", 22 : "nd", 23 : "rd", 31 : "st" },
			txt_months = ["", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
		
		var pad = function(n, c){ return ((n += "").length < c) ? new Array(++c - n.length).join("0") + n : n };
		
		var f = {
			d:function(){return pad(f.j(),2)},
			D:function(){t=f.l();return t.substr(0,3)},
			j:function(){return jsdate.getDate()},
			l:function(){return txt_weekdays[f.w()]},
			N:function(){return f.w()+1},
			S:function(){return txt_ordin[f.j()]?txt_ordin[f.j()]:"th"},
			w:function(){return jsdate.getDay()},
			z:function(){return((jsdate-new Date(jsdate.getFullYear()+"/1/1"))/864e5>>0)},
			W:function(){
				a=f.z();
				b=364+f.L()-a;
				nd=(new Date(jsdate.getFullYear()+"/1/1").getDay()||7)-1;
				nd2=new Date(jsdate.getFullYear()-1+"/12/31");
				if(b<=2&&((jsdate.getDay()||7)-1)<=2-b)return 1;
				else if(a<=2&&nd>=4&&a>=(6-nd))return date("W",Math.round(nd2.getTime()/1000));
				else return(1+(nd<=3?((a+nd)/7):(a-(7-nd))/7)>>0);
			},
			F:function(){return txt_months[f.n()]},
			m:function(){return pad(f.n(),2)},
			M:function(){t=f.F();return t.substr(0,3)},
			n:function(){return jsdate.getMonth()+1},
			t:function(){
				if((n=jsdate.getMonth()+1)==2)return 28+f.L();
				else if(n&1&&n<8||!(n&1)&&n>7)return 31;
				else return 30;
			},
			L:function(){y=f.Y();return((!(y&3)&&(y%1e2||!(y%4e2)))?1:0)},
			Y:function(){return jsdate.getFullYear()},
			y:function(){return(jsdate.getFullYear()+"").slice(2)},
			a:function(){return jsdate.getHours()>11?"pm":"am"},
			A:function(){return f.a().toUpperCase()},
			B:function(){
				off=(jsdate.getTimezoneOffset()+60)*60;
				theSeconds=(jsdate.getHours()*3600)+(jsdate.getMinutes()*60)+jsdate.getSeconds()+off;
				beat=Math.floor(theSeconds/86.4);
				if(beat>1000)beat-=1000;
				if(beat<0)beat+=1000;
				if((String(beat)).length==1)beat="00"+beat;
				if((String(beat)).length==2)beat="0"+beat;
				return beat;
			},
			g:function(){return jsdate.getHours()%12||12},
			G:function(){return jsdate.getHours()},
			h:function(){return pad(f.g(),2)},
			H:function(){return pad(jsdate.getHours(),2)},
			i:function(){return pad(jsdate.getMinutes(),2)},
			s:function(){return pad(jsdate.getSeconds(),2)},
			O:function(){
				t=pad(Math.abs(jsdate.getTimezoneOffset()/60*100),4);
				if(jsdate.getTimezoneOffset()>0)t="-"+t;
				else t="+"+t;
				return t;
			},
			P:function(){O=f.O();return(O.substr(0,3)+":"+O.substr(3,2))},
			c:function(){return(f.Y()+"-"+f.m()+"-"+f.d()+"T"+f.h()+":"+f.i()+":"+f.s()+f.P())},
			U:function(){return Math.round(jsdate.getTime()/1000)}
		};
		return format.replace(/[\\]?([a-zA-Z])/g,function(t,s){
			if(t!=s)ret=s;
			else if(f[s])ret=f[s]();
			else ret=s;
			return ret;
		});
	}
});
