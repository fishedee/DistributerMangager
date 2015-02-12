function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<ul class="common_littlelist">\n\t';
 for( var i in list ){ 
__p+='\n\t\t<a href="'+
((__t=( list[i].link ))==null?'':_.escape(__t))+
'">\n\t\t\t<li data="'+
((__t=( i ))==null?'':_.escape(__t))+
'">\n\t\t\t\t<div class="img"><img src="'+
((__t=( list[i].image ))==null?'':_.escape(__t))+
'"/></div>\n\t\t\t\t<h1 class="text">'+
((__t=( list[i].text ))==null?'':_.escape(__t))+
'</h1>\n\t\t\t</li>\n\t\t</a>\n\t';
 } 
__p+='\n</ul>';
}
return __p;
}