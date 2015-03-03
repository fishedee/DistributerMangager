function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<div class="common_itemlistwithnum" id="'+
((__t=( id ))==null?'':_.escape(__t))+
'">\n\t<h2>'+
((__t=( title ))==null?'':_.escape(__t))+
'</h2>\n\t<ul class="items">\n\t\t';
 for( var i in item ){ 
__p+='\n\t\t\t<li data="'+
((__t=( i ))==null?'':_.escape(__t))+
'">\n\t\t\t\t<div class="info" onclick="'+
((__t=( $.func.invoke(item[i].checkClick) ))==null?'':__t)+
'">\n\t\t\t\t\t<div class="checked active"></div>\n\t\t\t\t\t'+
((__t=( item[i].itembriefEl ))==null?'':__t)+
'\n\t\t\t\t</div>\n\t\t\t\t<div class="numinfo">\n\t\t\t\t\t<div class="del" onclick="'+
((__t=( $.func.invoke(item[i].delClick) ))==null?'':__t)+
'">删除</div>\n\t\t\t\t\t'+
((__t=( item[i].numchooserEl ))==null?'':__t)+
'\n\t\t\t\t</div>\n\t\t\t</li>\n\t\t';
 } 
__p+='\n\t</ul>\n</div>';
}
return __p;
}