function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<div class="common_numchooser" id="'+
((__t=( id ))==null?'':_.escape(__t))+
'">\n\t<span class="tip1">'+
((__t=( tip1 ))==null?'':_.escape(__t))+
'</span><span class="decrease" onclick="'+
((__t=( $.func.invoke(decreaseClick) ))==null?'':__t)+
'">-</span><input type="text" value="'+
((__t=( quantity ))==null?'':_.escape(__t))+
'"/><span class="increase" onclick="'+
((__t=( $.func.invoke(increaseClick) ))==null?'':__t)+
'">+</span><span class="tip2">'+
((__t=( tip2 ))==null?'':_.escape(__t))+
'</span>\n</div>';
}
return __p;
}