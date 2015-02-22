function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<ul id="common_address">\n\t<li class="name">\n\t\t<span>姓名：</span>\n\t\t<div class="input"><input type="text" value="'+
((__t=( name ))==null?'':_.escape(__t))+
'"></div>\n\t</li>\n\t<li class="province">\n\t\t<span>省份：</span>\n\t\t<div class="input">'+
((__t=( provinceSelectListEl ))==null?'':__t)+
'</div>\n\t</li>\n\t<li class="city">\n\t\t<span>城市：</span>\n\t\t<div class="input">'+
((__t=( citySelectListEl ))==null?'':__t)+
'</div>\n\t</li>\n\t<li class="address">\n\t\t<span>地址：</span>\n\t\t<div class="input"><input type="text" value="'+
((__t=( address ))==null?'':_.escape(__t))+
'"></div>\n\t</li>\n\t<li class="phone">\n\t\t<span>手机号码：</span>\n\t\t<div class="input"><input type="text" value="'+
((__t=( phone ))==null?'':_.escape(__t))+
'"></div>\n\t</li>\n\t<li class="payment">\n\t\t<span>支付方式：</span>\n\t\t<div class="input">'+
((__t=( paymentSelectListEl ))==null?'':__t)+
'</div>\n\t</li>\n</ul>';
}
return __p;
}