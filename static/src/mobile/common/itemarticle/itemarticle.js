/*
*@requrie itemarticle.less
*/
var $ = require('../core/core.js');
var uedit = require('/fishstrap/util/uedit.js');
var ItemBrief = require('../itembrief/itembrief.js');
function itemarticle(args){
	args.itembrief = new ItemBrief(args).el;
	var totalWidth = $('#body').width();
	var articleWith = totalWidth - 12*2;
	args.content = uedit.parse(args.content,articleWith);

	var template = __inline('itemarticleTpl.tpl');
	var el = template(args);
	return {
		el:el
	}
}
return itemarticle;