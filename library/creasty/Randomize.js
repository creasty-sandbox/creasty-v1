
creasty.creasty("creasty.Randomize", (function(){
	var rand = Math.random,
		alphabet = new String("abcdefghijklmnopqrstuvwxyz");
	
	function numRng(n1, n2){
		var lo = Math.min(n1, n2), hi = Math.max(n1, n2);
		return (parseInt(rand() * hi, 0) % (hi - lo + 1)) + lo;
	}
	function alpha(){
		return alphabet.charAt(numRng(0, alphabet.length - 1));
	}
	function digit(maxnumber){
		return parseInt(rand() * (Math.pow(10, maxnumber || 1)), 0);
	}
	
	return{
		toDigitLimit : digit,
		inNumberRange : numRng,
		toAlpha : alpha,
		inAlphaRange : function(a1, a2){
			return alphabet.charAt(numRng(alphabet.indexOf(a1), alphabet.indexOf(a2)));
		},
		toId : function(num){
			return alpha() + digit(num || 3);
		}
	};
})());
