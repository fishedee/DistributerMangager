function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<div id="common_banner">\n\t<ul class="images">\n\t\t';
 for( var i = 0 ; i != images.length ; ++i ){ 
__p+='\n\t\t\t<li data="'+
((__t=( i ))==null?'':_.escape(__t))+
'">\n\t\t\t\t<a href="'+
((__t=( images[i].link ))==null?'':_.escape(__t))+
'">\n\t\t\t\t\t<img src="'+
((__t=( images[i].image ))==null?'':_.escape(__t))+
'"/>\n\t\t\t\t</a>\n\t\t\t</li>\n\t\t';
 } 
__p+='\n\t</ul>\n\t<ul class="points">\n\t\t';
 for( var i = 0 ; i != images.length ; ++i ){ 
__p+='\n\t\t\t<li data="'+
((__t=( i ))==null?'':_.escape(__t))+
'">\n\t\t\t</li>\n\t\t';
 } 
__p+='\n\t</ul>\n</div>';
}
return __p;
}