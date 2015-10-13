/*
*@requrie bigbiglist.less
*/
var $ = require('../core/core.js');
function bigbiglist(args){
	var template = __inline('bigbiglistTpl.tpl');
	var el = template({list:args});
	return {
		el:el
	}
}
return bigbiglist;