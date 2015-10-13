/*
*@requrie deallist.less
*/
var $ = require('../core/core.js');
function deallist(args){
	args.list = args;
	args.id = _.uniqueId('common_deallist_');
	args.template = __inline('deallistTpl.tpl');
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
return deallist;