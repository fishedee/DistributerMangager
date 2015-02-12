define('mobile/common/headersearch/headersearch.js', function(require, exports, module){

/*
*@requrie headersearch.less
*/
var $ = require('mobile/common/core/core.js');
function headersearch(){
	var args = {
		click:function(){
			location.href = "search.html";
		}
	};
	var template = function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<div id="common_headersearch">\n\t<div class="button" onclick="'+
((__t=( $.func.invoke(click) ))==null?'':__t)+
'">搜索</div>\n\t<div class="input">\n\t\t<span></span><div><input type="text" placeholder="搜索商品"/></div>\n\t</div>\n</div>';
}
return __p;
};
	var el = template(args);
	return {
		el:el
	}
}
return headersearch;

});