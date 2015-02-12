/*
*@require itemdialog.less
*/
var $ = require('../core/core.js');
var ItemBriefWithNum = require('../itembriefwithnum/itembriefwithnum.js');
function itemdialog(args){
	var itembriefwithnum = new ItemBriefWithNum($.extend({quantity:1},args));
	args.itembriefwithnum = itembriefwithnum.el;
	args.cancelClick = function(){
		var target = $('#common_itemdialog');
		target.remove();
	}
	var tempConfirmClick = args.confirmClick;
	args.confirmClick = function(){
		var target = $('#common_itemdialog');
		var value = itembriefwithnum.get();
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