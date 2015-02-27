function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<div class="common_itembrief">\n\t<div class="img"><img src="'+
((__t=( image ))==null?'':_.escape(__t))+
'"/></div>\n\t<div class="info">\n\t\t<h1>'+
((__t=( title ))==null?'':_.escape(__t))+
'</h1>\n\t\t';
 if( _.isUndefined(this.summary) == false ){ 
__p+='\n\t\t\t<p>'+
((__t=( summary ))==null?'':_.escape(__t))+
'</p>\n\t\t';
 } 
__p+='\n\t\t<div class="price">价格：<span class="current">￥'+
((__t=( price ))==null?'':_.escape(__t))+
'</span>';
 if( _.isUndefined(this.oldprice) == false ){ 
__p+='<span class="old">￥'+
((__t=( oldprice ))==null?'':_.escape(__t))+
'</span>';
 } 
__p+='</div>\n\t\t';
 if( _.isUndefined(this.buyQuantity) == false ){ 
__p+='\n\t\t\t<div class="stock">购买数量：<span class="current">'+
((__t=( buyQuantity ))==null?'':_.escape(__t))+
'</span></div>\n\t\t';
 } 
__p+='\n\t\t';
 if( _.isUndefined(this.stock) == false ){ 
__p+='\n\t\t\t<div class="stock">库存：<span class="current">'+
((__t=( stock ))==null?'':_.escape(__t))+
'</span></div>\n\t\t';
 } 
__p+='\n\t</div>\n</div>';
}
return __p;
}