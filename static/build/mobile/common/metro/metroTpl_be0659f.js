function(obj){
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
}