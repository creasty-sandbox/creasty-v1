
require("creasty.Cookie").done(function(){
	creasty.create("creasty.Membership");
	
	creasty.Membership = function(){
/*
		var gn_screenName=$("#screenName>a"),
		gn_signout=$("#signout");
		if(gn_signout && gn_screenName){
			var $_ScreenName=creasty.Cookie.get("SCNE"),
			$_RememberMe=creasty.Cookie.get("REME"),
			$_AuthenticCreastyId=creasty.Cookie.get("ACID");
			
			if($_ScreenName){
				gn_screenName.text("ようこそ、"+$_ScreenName+"さん");
			}
			if($_AuthenticCreastyId){
				gn_signout.removeClass("hide");
			}
		}
*/
	};
	
	creasty.Membership.prototype.renderPanel = function(){
/*
		<ul class="pipe compact">
			<li><a href="/go/account" title="アカウント">ようこそ</a></li>
			<li><a href="/go/signout">サインアウト</a></li>
			
			<li><a href="/go/support">サポート</a></li>
			<li><a href="/developer">デベロッパー</a></li>
		</ul>
*/
	};	
});

