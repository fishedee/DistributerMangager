define('mobile/common/metro/metro.js', function(require, exports, module){

/*
*@requrie metro.less
*/
var $ = require('mobile/common/core/core.js');
function metro(args){
	var template = function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<ul class="common_metro">\n\t';
 for( var i = 0 ; i != list.length ; ++i ){ 
__p+='\n\t\t<a href="'+
((__t=( list[i].link ))==null?'':__t)+
'" >\n\t\t\t<li data="'+
((__t=( i ))==null?'':__t)+
'" style="width:'+
((__t=( list[i].size ))==null?'':__t)+
';">\n\t\t\t\t<div class="container" style="background:'+
((__t=( list[i].color ))==null?'':__t)+
';">\n\t\t\t\t\t<span class="icon"><img src="'+
((__t=( list[i].icon ))==null?'':__t)+
'"/></span>\n\t\t\t\t\t<span class="text">'+
((__t=( list[i].title ))==null?'':__t)+
'</span>\n\t\t\t\t</div>\n\t\t\t</li>\n\t\t</a>\n\t';
 } 
__p+='\n</ul>';
}
return __p;
};
	var colors = [
		{size:'33.3%',color:'rgb(54,170,231)'},
		{size:'33.3%',color:'rgb(104,140,226)'},
		{size:'33.3%',color:'rgb(141,103,224)'},
		{size:'50%',color:'rgb(132,208,24)'},
		{size:'50%',color:'rgb(20,199,97)'},
		{size:'33.3%',color:'rgb(244,181,18)'},
		{size:'33.3%',color:'rgb(251,140,69)'},
		{size:'33.3%',color:'rgb(252,83,102)'},
	];
	for( var i = 0 ; i != args.length ; ++i )
		args[i] = $.extend(args[i],colors[i%colors.length]);
	var el = template({list:args});
	return {
		el:el
	}
}
return metro;

});