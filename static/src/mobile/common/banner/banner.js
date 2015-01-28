/*
*@requrie banner.less
*@require /fishstrap/lib/swipe.js
*/
var $ = require('../core/core.js');
function banner(args){
	var template = __inline('bannerTpl.tpl');
	var el = template(args);
	$.page.ready(function(){
		var interval;
		var target = $('#common_banner');
		function updatePoints(index){
			curId = ( index )% args.images.length;
			target.find('ul.points li.active').removeClass();
			target.find('ul.points li[data='+curId+']').addClass('active');
		}
		function start(){
			var elem = document.getElementById('common_banner_images');
			window.mySwipe = Swipe(elem, {
				startSlide: 0,
				speed: 300,
				auto: args.interval,
				continuous: true,
				transitionEnd: function(index, element) {
					updatePoints(index);
				},
			});
			target.find('ul.points li[data=0]').addClass('active');
		}
		start();
	});
	return {
		el:el
	}
}
return banner;