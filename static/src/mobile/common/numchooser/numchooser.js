/*
*@require numchooser.less
*/
var $ = require('../core/core.js');
function numchooser(args){
	args.decreaseClick = decreaseClick;
	args.increaseClick = increaseClick;
	args.id = _.uniqueId('common_numchooser_');
	var template = __inline('numchooserTpl.tpl');
	var el = template(args);
	function decreaseClick(){
		var target = $('#'+args.id).find('input');
		var value = get();
		target.val(value-1>=1?value-1:1);
	}
	function increaseClick(){
		var target = $('#'+args.id).find('input');
		var value = get();
		target.val(value+1);
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