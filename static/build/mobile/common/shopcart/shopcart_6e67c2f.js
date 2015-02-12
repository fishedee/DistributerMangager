define('mobile/common/shopcart/shopcart.js', function(require, exports, module){

/*
*@requrie shopcart.less
*/
var $ = require('mobile/common/core/core.js');
function shopcart(){
	var template = function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<a href="shopcart.html">\n\t<div id="common_shopcart">\n\t\t<span class="icon"></span>\n\t\t<span class="text">购物车</span>\n\t</div>\n</a>';
}
return __p;
};
	var el = template();
	return {
		el:el
	}
}
return shopcart;

});