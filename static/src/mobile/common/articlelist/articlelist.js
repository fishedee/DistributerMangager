/*
*@requrie articlelist.less
*/
var $ = require('../core/core.js');
function articlelist(args){
	var template = __inline('articlelistTpl.tpl');
	var el = template({list:args});
	return {
		el:el
	}
}
return articlelist;