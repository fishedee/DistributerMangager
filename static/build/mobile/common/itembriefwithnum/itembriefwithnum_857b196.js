define('mobile/common/itembriefwithnum/itembriefwithnum.js', function(require, exports, module){

/*
*@require mobile/common/itembriefwithnum/itembriefwithnum.less
*/
var $ = require('mobile/common/core/core.js');
var ItemBrief = require('mobile/common/itembrief/itembrief.js');
function itembriefwithnum(args){
	args.itembrief = new ItemBrief(args).el;
	args.id = _.uniqueId('common_itembriefwithnum_');
	var template = function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<div class="common_itembriefwithnum" id="'+
((__t=( id ))==null?'':_.escape(__t))+
'">\n\t'+
((__t=( itembrief ))==null?'':__t)+
'\n\t<div class="quantity">\n\t\t<span class="tip1">请选择数量：</span>\n\t\t<span class="decrease" onclick="decreaseClick">-</span><input type="text" value="'+
((__t=( quantity ))==null?'':_.escape(__t))+
'"/><span class="increase" onclick="decreaseClick">+</span>\n\t\t<span class="tip2">件</span>\n\t</div>\n</div>';
}
return __p;
};
	var el = template(args);
	function get(){
		var target = $('#'+args.id);
		return target.find('.quantity input').val();
	}
	return {
		el:el,
		get:get
	}
}
return itembriefwithnum;

});