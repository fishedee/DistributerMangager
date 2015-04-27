/*
*@requrie about.less
*/
var $ = require('../core/core.js');
function create(){
	var args = {};
	args.logo = __uri('logo.jpg');
	var template = __inline('aboutTpl.tpl');
	var el = template(args);
	return {
		el:el
	}
}
return create;