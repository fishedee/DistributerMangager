/*
*@requrie footerbutton.less
*/
var $ = require('../core/core.js');
var uedit = require('/fishstrap/util/uedit.js');
function footerbutton(args){
	var template = __inline('footerbuttonTpl.tpl');
	var el = template({list:args});
	return {
		el:el
	}
}
return footerbutton;