function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<h1 class="common_headertitle" id="'+
((__t=( id ))==null?'':_.escape(__t))+
'"><span class="tip">'+
((__t=( tip ))==null?'':_.escape(__t))+
'</span><span class="text">'+
((__t=( text ))==null?'':_.escape(__t))+
'</span></h1>';
}
return __p;
}