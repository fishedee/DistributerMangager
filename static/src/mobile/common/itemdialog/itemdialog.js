/*
*@require itemdialog.less
*/
var $ = require('../core/core.js');
var ItemBrief = require('../itembrief/itembrief.js');
var NumChooser = require('../numchooser/numchooser.js');
function itemdialog(args){
	var itembrief = new ItemBrief(args);
	var numchooser = new NumChooser({
		tip1:'请选择数量：',
		quantity:1,
		tip2:'件',
	});
	args.itembrief = itembrief.el;
	args.numchooser = numchooser.el;
	args.cancelClick = function(){
		var target = $('#common_itemdialog');
		target.remove();
	}
	var tempConfirmClick = args.confirmClick;
	args.confirmClick = function(){
		var target = $('#common_itemdialog');
		var value = numchooser.get();
		target.remove();
		tempConfirmClick(value);
	}
	var template = __inline('itemdialogTpl.tpl');
	var el = template(args);
	return {
		el:el
	}
}
return itemdialog;