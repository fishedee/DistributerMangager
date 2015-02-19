define('mobile/common/numchooser/numchooser.js', function(require, exports, module){

/*
*@require mobile/common/numchooser/numchooser.less
*/
var $ = require('mobile/common/core/core.js');
function numchooser(args){
	if( _.isUndefined(args.change) == true )
		args.change = _.noop;
	args.decreaseClick = decreaseClick;
	args.increaseClick = increaseClick;
	args.id = _.uniqueId('common_numchooser_');
	var template = function(obj){
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
'" onchange="'+
((__t=( $.func.invoke(change) ))==null?'':__t)+
'"/><span class="increase" onclick="'+
((__t=( $.func.invoke(increaseClick) ))==null?'':__t)+
'">+</span><span class="tip2">'+
((__t=( tip2 ))==null?'':_.escape(__t))+
'</span>\n</div>';
}
return __p;
};
	var el = template(args);
	function decreaseClick(){
		var target = $('#'+args.id).find('input');
		var value = get();
		target.val(value-1>=1?value-1:1);
		args.change();
	}
	function increaseClick(){
		var target = $('#'+args.id).find('input');
		var value = get();
		target.val(value+1);
		args.change();
	}
	function get(){
		var target = $('#'+args.id).find('input');
		var value = parseInt(target.val());
		if( isNaN(value))
			value = 1;
		return value;
	}
	return {
		el:el,
		get:get
	}
}
return numchooser;

});