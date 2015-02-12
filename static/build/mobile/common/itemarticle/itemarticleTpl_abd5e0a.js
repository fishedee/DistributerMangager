function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<div id="common_itemarticle">\n\t<div class="briefinfo">\n\t\t<div class="img"><img src="'+
((__t=( image ))==null?'':_.escape(__t))+
'"/></div>\n\t\t<div class="info">\n\t\t\t<h1>'+
((__t=( title ))==null?'':_.escape(__t))+
'</h1>\n\t\t\t<p>'+
((__t=( summary ))==null?'':_.escape(__t))+
'</p>\n\t\t\t<div class="price">价格：<span class="current">￥'+
((__t=( (price/100).toFixed(2) ))==null?'':_.escape(__t))+
'</span><span class="old">￥'+
((__t=( (price/100).toFixed(2) ))==null?'':_.escape(__t))+
'</span></div>\n\t\t\t<div class="stock">库存：<span class="current">'+
((__t=( stock ))==null?'':_.escape(__t))+
'</span></div>\n\t\t</div>\n\t</div>\n\t<div class="content">'+
((__t=( content ))==null?'':__t)+
'</div>\n</div>';
}
return __p;
}