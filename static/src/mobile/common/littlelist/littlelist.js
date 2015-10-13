/*
*@requrie littlelist.less
*/
var $ = require('../core/core.js');
function littlelist(args){
	var template = __inline('littlelistTpl.tpl');
	var el = template({list:args});
	return {
		el:el
	}
}
return littlelist;