define('mobile/common/footerbutton/footerbutton.js', function(require, exports, module){

/*
*@requrie footerbutton.less
*/
var $ = require('mobile/common/core/core.js');
var uedit = require('fishstrap/util/uedit.js');
function footerbutton(args){
	var template = function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<ul id="common_footerbutton">\n\t';
 for( var i in list ){ 
__p+='\n\t';
 var width = 1/list.length * 100 ; 
__p+='\n\t\t<li data="'+
((__t=( i ))==null?'':_.escape(__t))+
'" class="'+
((__t=( list[i].name ))==null?'':_.escape(__t))+
'" style="width:'+
((__t=( width ))==null?'':_.escape(__t))+
'%;" onclick="'+
((__t=( $.func.invoke(click) ))==null?'':__t)+
'">\n\t\t\t<span>'+
((__t=( list[i].text ))==null?'':_.escape(__t))+
'</span>\n\t\t</li>\n\t';
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
return footerbutton;

});