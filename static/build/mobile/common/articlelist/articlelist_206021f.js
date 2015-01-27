define('mobile/common/articlelist/articlelist.js', function(require, exports, module){

/*
*@requrie articlelist.less
*/
var $ = require('mobile/common/core/core.js');
function articlelist(args){
	var template = function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<ul id="common_articlelist">\n\t';
 for( var i = 0 ; i != list.length ; ++i ){ 
__p+='\n\t\t<li data="'+
((__t=( i ))==null?'':__t)+
'">\n\t\t\t<a href="'+
((__t=( list[i].link ))==null?'':__t)+
'">\n\t\t\t\t<h1>'+
((__t=( list[i].title ))==null?'':__t)+
'</h1>\n\t\t\t\t<img src="'+
((__t=( list[i].image ))==null?'':__t)+
'"/>\n\t\t\t\t<p>'+
((__t=( list[i].summary ))==null?'':__t)+
'</p>\n\t\t\t\t<span>更多</span>\n\t\t\t</a>\n\t\t</li>\n\t';
 } 
__p+='\n</ul>';
}
return __p;
};
	var el = template({list:args});
	return {
		el:el
	}
}
return articlelist;

});