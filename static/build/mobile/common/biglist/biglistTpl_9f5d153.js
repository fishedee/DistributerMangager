function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<ul class="common_biglist">\n\t';
 for( var i in list ){ 
__p+='\n\t\t<a href="'+
((__t=( list[i].link ))==null?'':_.escape(__t))+
'">\n\t\t\t<li data="'+
((__t=( i ))==null?'':_.escape(__t))+
'">\n\t\t\t\t'+
((__t=( list[i].itembrief ))==null?'':__t)+
'\n\t\t\t</li>\n\t\t</a>\n\t';
 } 
__p+='\n</ul>';
}
return __p;
}