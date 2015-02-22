function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<ul class="common_headerbutton">\n\t';
 for( var i = 0 ; i != list.length ; ++i ){ 
__p+='\n\t';
 var width = (1/list.length)*100; 
__p+='\n\t\t<li style="width:'+
((__t=( width ))==null?'':_.escape(__t))+
'%" data="'+
((__t=( i ))==null?'':_.escape(__t))+
'" onclick="'+
((__t=( $.func.invoke(list[i].click) ))==null?'':__t)+
'">\n\t\t\t<p class="number">'+
((__t=( list[i].number ))==null?'':_.escape(__t))+
'</p>\n\t\t\t<p class="name">'+
((__t=( list[i].name ))==null?'':_.escape(__t))+
'</p>\n\t\t</li>\n\t';
 } 
__p+='\n</ul>';
}
return __p;
}