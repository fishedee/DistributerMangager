/*
*@requrie shopcart.less
*/
var $ = require('../core/core.js');
function shopcart(){
	var template = __inline('shopcartTpl.tpl');
	var el = template();
	return {
		el:el
	}
}
return shopcart;