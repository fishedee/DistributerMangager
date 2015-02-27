/*
*@requrie selectlist.less
*/
var $ = require('../core/core.js');
function selectlist(args){
	args.id = _.uniqueId('common_selectlist_');
	args.template = __inline('selectlistTpl.tpl');
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