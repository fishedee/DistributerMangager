/*
*@requrie headertitle.less
*/
var $ = require('../core/core.js');
function headertitle(args){
	args.id = _.uniqueId('common_headertitle_');
	args.template = __inline('headertitleTpl.tpl');
	var el = args.template(args);
	function set(data){
		var target = $('#'+args.id);
		args = $.extend(args,data);
		target.replaceWith(args.template(args));
	}
	return {
		el:el,
		set:set
	}
}
return headertitle;