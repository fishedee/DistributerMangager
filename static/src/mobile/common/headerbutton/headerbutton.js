/*
*@requrie headerbutton.less
*/
var $ = require('../core/core.js');
function headerbutton(data){
	args = {};
	args.id = _.uniqueId('common_headerbutton_');
	args.list = data;
	args.template = __inline('headerbuttonTpl.tpl');
	var el = args.template(args);
	function set(data){
		args.list = data;
		var newEl = args.template(args);
		var target = $('#'+args.id);
		target.replaceWith(newEl);
	}
	return {
		el:el,
		set:set
	}
}
return headerbutton;