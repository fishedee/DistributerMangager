/*
*@requrie header.less
*/
var $ = require('../core/core.js');
function header(data){
	var template = __inline('headerTpl.tpl');
	var el = template(data);
	return {
		el:el
	}
}
return header;