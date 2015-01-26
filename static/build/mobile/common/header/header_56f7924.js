define('mobile/common/header/header.js', function(require, exports, module){

/*
*@requrie header.less
*/
var $ = require('mobile/common/core/core.js');
function header(data){
	var template = function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<div id="common_header">\n\t<a href="'+
((__t=( backLink ))==null?'':__t)+
'">\n\t\t<button class="backicon"></button>\n\t</a>\n\t';
 for( var i in button ){ 
__p+='\n\t<a href="'+
((__t=( button[i].link ))==null?'':__t)+
'">\n\t\t<button class="'+
((__t=( button[i].name ))==null?'':__t)+
'icon"></button>\n\t</a>\n\t';
 } 
__p+='\n\t<p class="title">'+
((__t=( title ))==null?'':__t)+
'</p>\n</div>\n<div id="common_header_padding"></div>';
}
return __p;
};
	var el = template(data);
	return {
		el:el
	}
}
return header;

});