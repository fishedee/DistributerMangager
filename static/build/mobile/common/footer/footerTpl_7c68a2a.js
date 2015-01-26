function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<div id="common_footer_padding"></div>\n<ul id="common_footer">\n\t<a href="company.html">\n\t\t<li class="company" state="'+
((__t=( companyState ))==null?'':__t)+
'">\n\t\t\t<span class="icon"></span>\n\t\t\t<span class="text">公司</span>\n\t\t</li>\n\t</a>\n\t<a href="item.html">\n\t\t<li class="item" state="'+
((__t=( itemState ))==null?'':__t)+
'">\n\t\t\t<span class="icon"></span>\n\t\t\t<span class="text">商品</span>\n\t\t</li>\n\t</a>\n\t<a href="deal.html">\n\t\t<li class="deal" state="'+
((__t=( dealState ))==null?'':__t)+
'">\n\t\t\t<span class="icon"></span>\n\t\t\t<span class="text">订单</span>\n\t\t</li>\n\t</a>\n\t<a href="me.html">\n\t\t<li class="me" state="'+
((__t=( meState ))==null?'':__t)+
'">\n\t\t\t<span class="icon"></span>\n\t\t\t<span class="text">我</span>\n\t\t</li>\n\t</a>\n\t\n</ul>';
}
return __p;
}