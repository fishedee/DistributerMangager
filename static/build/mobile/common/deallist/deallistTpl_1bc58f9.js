function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<ul class="common_deallist" id="'+
((__t=( id ))==null?'':_.escape(__t))+
'">\n\t';
 for( var i = 0 ; i != list.length ; ++i ){ 
__p+='\n\t\t<a href="'+
((__t=( list[i].link ))==null?'':_.escape(__t))+
'">\n\t\t\t<li data="'+
((__t=( i ))==null?'':_.escape(__t))+
'">\n\t\t\t\t<div class="img"><img src="'+
((__t=( list[i].image ))==null?'':_.escape(__t))+
'"/></div>\n\t\t\t\t<div class="info">\n\t\t\t\t\t<div class="id"><span class="tip">订单号：</span><span class="text">'+
((__t=( list[i].id ))==null?'':_.escape(__t))+
'</span></div>\n\t\t\t\t\t<div class="state"><span class="tip">订单状态：</span><span class="text">'+
((__t=( list[i].state ))==null?'':_.escape(__t))+
'</span></div>\n\t\t\t\t\t<div class="price"><span class="tip">订单金额：</span><span class="text">￥'+
((__t=( list[i].price ))==null?'':_.escape(__t))+
'</span></div>\n\t\t\t\t</div>\n\t\t\t</li>\n\t\t</a>\n\t';
 } 
__p+='\n</ul>';
}
return __p;
}