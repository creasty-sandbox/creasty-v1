$(function(){
	var a = 2e3,
		b = $("#content-type"),
		c = $("#content-type2"),
		d = 1,
		e = 0,
		f = window.sellable_things.length;
	
	function g(a){
		var e = d == 1 ? b : c,
			f = d == 1 ? c : b;
		f.text(a), f.show("drop", {
			direction: "up"
		}, 300), e.hide("drop", {
			direction: "down"
		}, 300), d = !d
	}
	
	setInterval(function(){
		e > f - 1 && (e = 0), g(window.sellable_things[e++])
	}, a), $(".link").click(function(a) {
		if (a.target.href == undefined) return window.location = $(this).data("url"), !1
	})
});

/*

<span id="content-type-wrapper"><span id="content-type">未発表の音楽</span><span id="content-type2">未発表の音楽</span></span>
*/