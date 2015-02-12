/*
*@requrie itembrief.less
*/
var $ = require('../core/core.js');
function itembrief(args){
	var template = __inline('itembriefTpl.tpl');
	var el = template(args);
	return {
		el:el
	}
}
return itembrief;