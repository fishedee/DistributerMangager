function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<div id="common_article">\n\t<h1>'+
((__t=( title ))==null?'':_.escape(__t))+
'</h1>\n\t<div id="'+
((__t=( id ))==null?'':_.escape(__t))+
'">\n\t\t'+
((__t=( content ))==null?'':__t)+
'\n\t</div>\n</div>';
}
return __p;
}