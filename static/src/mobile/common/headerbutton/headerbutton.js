/*
*@requrie headerbutton.less
*/
var $ = require('../core/core.js');
function headerbutton(data){
	var template = __inline('headerbuttonTpl.tpl');
	var el = template({list:data});
	return {
		el:el
	}
}
return headerbutton;