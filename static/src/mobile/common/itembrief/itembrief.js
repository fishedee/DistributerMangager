/*
*@requrie itembrief.less
*/
var $ = require('../core/core.js');
function itembrief(args){
	args.template = __inline('itembriefTpl.tpl');
	var el = args.template(args);
	return {
		el:el
	}
}
return itembrief;