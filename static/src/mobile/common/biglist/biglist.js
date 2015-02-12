/*
*@require biglist.less
*/
var $ = require('../core/core.js');
var ItemBrief = require('../itembrief/itembrief.js');
function biglist(args){
	for(var i in args ){
		args[i].itembrief = new ItemBrief(args[i]).el;
	}
	var template = __inline('biglistTpl.tpl');
	var el = template({list:args});
	return {
		el:el
	}
}
return biglist;