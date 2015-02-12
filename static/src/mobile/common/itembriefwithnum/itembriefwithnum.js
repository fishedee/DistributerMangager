/*
*@require itembriefwithnum.less
*/
var $ = require('../core/core.js');
var ItemBrief = require('../itembrief/itembrief.js');
function itembriefwithnum(args){
	args.decreaseClick = decreaseClick;
	args.increaseClick = increaseClick;
	args.itembrief = new ItemBrief(args).el;
	args.id = _.uniqueId('common_itembriefwithnum_');
	var template = __inline('itembriefwithnumTpl.tpl');
	var el = template(args);
	function decreaseClick(){
		var target = $('#'+args.id).find('.quantity input');
		var value = get();
		target.val(value-1>=1?value-1:1);
	}
	function increaseClick(){
		var target = $('#'+args.id).find('.quantity input');
		var value = get();
		target.val(value+1);
	}
	function get(){
		var target = $('#'+args.id).find('.quantity input');
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
return itembriefwithnum;