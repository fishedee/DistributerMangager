/*
*@requrie itembrief.less
*/
var $ = require('../core/core.js');
var NumChooser = require('../numchooser/numchooser.js');
function itembrief(args){
	args.template = __inline('itembriefTpl.tpl');
	var el = args.template(args);
	function get(){
		return args;
	}
	return {
		el:el,
		get:get
	}
}
return itembrief;