define('mobile/common/banner/banner.js', function(require, exports, module){

/*
*@requrie banner.less
*@require fishstrap/lib/swipe.js
*/
var $ = require('mobile/common/core/core.js');
function banner(args){
	var template = function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<div id="common_banner">\n\t<ul class=\'swipe images\' id=\'common_banner_images\'>\n\t\t<div class=\'swipe-wrap wrapper\'>\n\t\t\t';
 for( var i = 0 ; i != images.length ; ++i ){ 
__p+='\n\t\t\t\t<li data="'+
((__t=( i ))==null?'':_.escape(__t))+
'">\n\t\t\t\t\t<a href="'+
((__t=( images[i].link ))==null?'':_.escape(__t))+
'">\n\t\t\t\t\t\t<img src="'+
((__t=( images[i].image ))==null?'':_.escape(__t))+
'"/>\n\t\t\t\t\t</a>\n\t\t\t\t</li>\n\t\t\t';
 } 
__p+='\n\t\t</div>\n\t</ul>\n\t<ul class="points">\n\t\t';
 for( var i = 0 ; i != images.length ; ++i ){ 
__p+='\n\t\t\t<li data="'+
((__t=( i ))==null?'':_.escape(__t))+
'">\n\t\t\t</li>\n\t\t';
 } 
__p+='\n\t</ul>\n</div>';
}
return __p;
};
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

});