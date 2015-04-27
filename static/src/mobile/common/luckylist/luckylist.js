/*
*@requrie luckylist.less
*/
var $ = require('../core/core.js');
function create(args){
	args.list = args;
	args.id = _.uniqueId('common_luckylist_');
	args.template = __inline('luckylistTpl.tpl');
	var el = args.template(args);
	function set(data){
		args = $.extend(args,{list:data});
		var newEl = args.template(args);
		var target = $('#'+args.id);
		target.replaceWith(newEl);
	}
	return {
		el:el,
		set:set
	}
}
return create;