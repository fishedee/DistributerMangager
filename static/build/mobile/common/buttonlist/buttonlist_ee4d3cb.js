define('mobile/common/buttonlist/buttonlist.js', function(require, exports, module){

/*
*@requrie buttonlist.less
*/
var $ = require('mobile/common/core/core.js');
function buttonlist(data){
	var template = function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<ul class="common_buttonlist">\n\t';
 for( var i = 0 ; i != list.length ; ++i ){ 
__p+='\n\t\t<a href="'+
((__t=( list[i].link ))==null?'':_.escape(__t))+
'">\n\t\t\t<li data="'+
((__t=( i ))==null?'':_.escape(__t))+
'">'+
((__t=( list[i].name ))==null?'':_.escape(__t))+
'</li>\n\t\t</a>\n\t';
 } 
__p+='\n</ul>';
}
return __p;
};
	var el = template({list:data});
	return {
		el:el
	}
}
return buttonlist;

});