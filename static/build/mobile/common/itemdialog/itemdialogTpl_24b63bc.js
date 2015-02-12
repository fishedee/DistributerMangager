function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<div id="common_itemdialog">\n\t<div class="dialog">\n\t\t<div class="cacnelClick" onclick="'+
((__t=( cancelClick ))==null?'':_.escape(__t))+
'">×</div>\n\t\t'+
((__t=( itembriefwithnum ))==null?'':__t)+
'\n\t\t<div class="'+
((__t=( confirmName ))==null?'':_.escape(__t))+
'" onclick="'+
((__t=( confirmClick ))==null?'':_.escape(__t))+
'">'+
((__t=( confirmText ))==null?'':_.escape(__t))+
'</div>\n\t</div>\n</div>';
}
return __p;
}