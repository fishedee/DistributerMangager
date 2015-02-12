define('mobile/common/itemdialog/itemdialog.js', function(require, exports, module){

/*
*@require mobile/common/itemdialog/itemdialog.less
*/
var $ = require('mobile/common/core/core.js');
var ItemBriefWithNum = require('mobile/common/itembriefwithnum/itembriefwithnum.js');
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
		target.remove();
		tempConfirmClick(itembriefwithnum.get());
	}
	var template = function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<div id="common_itemdialog">\n\t<div class="dialog">\n\t\t<div class="cacnelClick" onclick="'+
((__t=( $.func.invoke(cancelClick) ))==null?'':_.escape(__t))+
'">×</div>\n\t\t'+
((__t=( itembriefwithnum ))==null?'':__t)+
'\n\t\t<div class="confirmClik '+
((__t=( confirmName ))==null?'':_.escape(__t))+
'" onclick="'+
((__t=( confirmClick ))==null?'':_.escape(__t))+
'">'+
((__t=( confirmText ))==null?'':_.escape(__t))+
'</div>\n\t</div>\n</div>';
}
return __p;
};
	var el = template(args);
	return {
		el:el
	}
}
return itemdialog;

});