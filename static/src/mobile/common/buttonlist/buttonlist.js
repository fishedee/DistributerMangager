/*
*@requrie buttonlist.less
*/
var $ = require('../core/core.js');
function buttonlist(data){
	var template = __inline('buttonlistTpl.tpl');
	var el = template({list:data});
	return {
		el:el
	}
}
return buttonlist;