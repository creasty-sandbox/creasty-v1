﻿$(function () {
	var $fader = $('#fader');

	var src = '/images/home/portrait.jpg';

	var bgi = new Image();
	bgi.src = src;

	bgi.onload = function () {
		var $image = $('<img src="' + src + '" />').appendTo($fader);
		$image.css('visibility', 'none');
		$fader.imgLiquid({ responsive: true });
		$image.css('visibility', 'visible').hide().fadeIn(1000);
	};
});


require('module.jquery.fitText', '!ready').done(function () {
	$('h2')
	.children('span')
	.fitText(1.833, { minFontSize: 30 })
	.end()
	.children('em')
	.fitText(3.055, { minFontSize: 24 });
});

require('module.jquery.fluidGrid', '!ready').done(function () {
	$('.home-grid').fluidGrid({
		columns: 4,
		gutter: 20,
		minWidth: 200
	});
});


/*
jQuery Plugin: imgLiquid v0.7.7 / 01-01-13
jQuery plugin to resize images to fit in a container.
Copyright (c) 2012 Alejandro Emparan (karacas), twitter: @krc_ale
Dual licensed under the MIT and GPL licenses
https://github.com/karacas/imgLiquid
*/
;(function($){$.fn.extend({imgLiquid:function(options){var isIE= /*@cc_on!@*/ false;var totalItems=this.size();var processedItems=0;this.defaultOptions={};var settings=$.extend({fill:true,verticalAlign:"center",horizontalAlign:"center",fadeInTime:0,responsive:false,responsiveCheckTime:100,delay:0,removeBoxBackground:true,ieFadeInDisabled:true,useDataHtmlAttr:true,useCssAligns:false,imageRendering:"auto",onStart:null,onFinish:null,onItemResize:null,onItemStart:null,onItemFinish:null},this.defaultOptions,options);if(settings.onStart){settings.onStart()}return this.each(function($i){var $imgBox=$(this);var $img=$("img:first",$imgBox);if(!$img||$img===null||$img.size()===0){onError();return null}if($img.ILprocessed){process($imgBox,$img,$i);return}$img.ILprocessed=false;$img.ILerror=false;if(settings.onItemStart){settings.onItemStart($i,$imgBox,$img)}$img.fadeTo(0,0);$("img:not(:first)",$imgBox).css("display","none");$img.css({visibility:"visible","max-width":"none","max-height":"none",width:"auto",height:"auto",display:"block","image-rendering":settings.imageRendering});$img.removeAttr("width");$img.removeAttr("height");if(settings.delay<1){settings.delay=1}$imgBox.css({overflow:"hidden"});if(isIE&&settings.imageRendering=="optimizeQuality"){$img.css("-ms-interpolation-mode","bicubic")}if(settings.useCssAligns){var cha=$imgBox.css("text-align");var cva=$imgBox.css("vertical-align");if(cha=="left"||cha=="center"||cha=="right"){settings.horizontalAlign=cha}if(cva=="top"||cva=="middle"||cva=="bottom"||cva=="center"){settings.verticalAlign=cva}}if(settings.useDataHtmlAttr){if($imgBox.attr("data-imgLiquid-fill")=="true"){settings.fill=true}if($imgBox.attr("data-imgLiquid-fill")=="false"){settings.fill=false}if($imgBox.attr("data-imgLiquid-responsive")=="true"){settings.responsive=true}if($imgBox.attr("data-imgLiquid-responsive")=="false"){settings.responsive=false}if(Number($imgBox.attr("data-imgLiquid-fadeInTime"))>0){settings.fadeInTime=Number($imgBox.attr("data-imgLiquid-fadeInTime"))}var ha=$imgBox.attr("data-imgLiquid-horizontalAlign");var va=$imgBox.attr("data-imgLiquid-verticalAlign");if(ha=="left"||ha=="center"||ha=="right"){settings.horizontalAlign=ha}if(va=="top"||va=="middle"||va=="bottom"||va=="center"){settings.verticalAlign=va}}if(isIE&&settings.ieFadeInDisabled){settings.fadeInTime=0}function checkElementSize(){setTimeout(function(){$imgBox.actualSize=$imgBox.get(0).offsetWidth+($imgBox.get(0).offsetHeight/100000);if($imgBox.actualSize!==$imgBox.sizeOld){if($img.ILprocessed&&$imgBox.sizeOld!==undefined){if(settings.onItemResize){settings.onItemResize($i,$imgBox,$img)}if(settings.responsive){process($imgBox,$img,$i)}}}$imgBox.sizeOld=$imgBox.actualSize;checkElementSize()},settings.responsiveCheckTime)}if(settings.responsive||settings.onItemResize!==null){checkElementSize()}$img.load(function(){if(!Boolean($img.width()===0&&$img.height()===0)){setTimeout(function(){process($imgBox,$img,$i)},$i*settings.delay)}}).each(function(){if(this.complete){$img.trigger("load")}}).error(function(){onError();return null});function onError(){$img.ILerror=true;checkFinish($imgBox,$img,$i);$imgBox.css("visibility","hidden")}function process($imgBox,$img,$i){if(settings.fill==($imgBox.width()/$imgBox.height())>=($img.width()/$img.height())){$img.css({width:"100%",height:"auto"})}else{$img.css({width:"auto",height:"100%"})}var ha=settings.horizontalAlign.toLowerCase();var hdif=$imgBox.width()-$img.width();var margL=0;if(ha=="center"||ha=="middle"){margL=hdif/2}if(ha=="right"){margL=hdif}$img.css("margin-left",Math.round(margL));var va=settings.verticalAlign.toLowerCase();var vdif=$imgBox.height()-$img.height();var margT=0;if(va=="center"||va=="middle"){margT=vdif/2}if(va=="bottom"){margT=vdif}$img.css("margin-top",Math.round(margT));if(!$img.ILprocessed){if(settings.removeBoxBackground){$imgBox.css("background-image","none")}$img.fadeTo(settings.fadeInTime,1);$img.ILprocessed=true;if(settings.onItemFinish){settings.onItemFinish($i,$imgBox,$img)}checkFinish($imgBox,$img,$i)}}function checkFinish($imgBox,$img,$i){processedItems++;if(processedItems==totalItems){if(settings.onFinish){settings.onFinish()}}}})}})})(jQuery);