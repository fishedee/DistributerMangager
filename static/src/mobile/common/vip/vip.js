/*
*@requrie vip.less
*/
var $ = require('../core/core.js');
function create(args){
	var template = __inline('vipTpl.tpl');
	var el = template(args);
	return {
		el:el
	}
}
return create;