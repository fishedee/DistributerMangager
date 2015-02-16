define('mobile/common/headertitle/headertitle.js', function(require, exports, module){

/*
*@requrie headertitle.less
*/
var $ = require('mobile/common/core/core.js');
function headertitle(args){
	var template = function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<h1 class="common_headertitle"><span class="tip">'+
((__t=( tip ))==null?'':_.escape(__t))+
'</span><span class="text">'+
((__t=( text ))==null?'':_.escape(__t))+
'</span></h1>';
}
return __p;
};
	var el = template(args);
	return {
		el:el
	}
}
return headertitle;

});