define('mobile/common/selectlist/selectlist.js', function(require, exports, module){

/*
*@requrie selectlist.less
*/
var $ = require('mobile/common/core/core.js');
function selectlist(args){
	args.id = _.uniqueId('common_selectlist_');
	args.template = function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<div class="common_selectlist" id="'+
((__t=( id ))==null?'':_.escape(__t))+
'" >\n\t<select onchange="'+
((__t=( $.func.invoke(change) ))==null?'':__t)+
'">\n\t\t';
 for( var i = 0 ; i != list.length ; ++i ){ 
__p+='\n\t\t\t';
 if( list[i].value == value ){ 
__p+='\n\t\t\t\t<option value ="'+
((__t=( list[i].value ))==null?'':_.escape(__t))+
'" selected="selected">'+
((__t=( list[i].name ))==null?'':_.escape(__t))+
'</option>\n\t\t\t';
 }else{ 
__p+='\n\t\t\t\t<option value ="'+
((__t=( list[i].value ))==null?'':_.escape(__t))+
'">'+
((__t=( list[i].name ))==null?'':_.escape(__t))+
'</option>\n\t\t\t';
 } 
__p+='\n\t\t';
 } 
__p+='\n\t</select>\n\t<span class="downicon"></span>\n</div>';
}
return __p;
};
	var el = args.template(args);
	function set(data){
		var target = $('#'+args.id);
		args = $.extend(args,data);
		var newEl = args.template(args);
		target.replaceWith(newEl);
	}
	function get(){
		var target = $('#'+args.id);
		return target.find('select').val();
	}
	return {
		set:set,
		get:get,
		el:el
	}
}
return selectlist;

});