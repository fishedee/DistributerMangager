/*
*@requrie headertitle.less
*/
var $ = require('../core/core.js');
function headertitle(args){
	var template = __inline('headertitleTpl.tpl');
	var el = template(args);
	return {
		el:el
	}
}
return headertitle;