/*
*@require itemlistwithnum.less
*/
var $ = require('../core/core.js');
var dialog = require('../dialog/dialog.js');
var ItemBrief = require('../itembrief/itembrief.js');
var NumChooser = require('../numchooser/numchooser.js');
function itemlistwithnum(args){
	for( var i = 0 ; i != args.item.length ; ++i ){
		(function (i){
			//基本信息
			args.item[i].itembrief = new ItemBrief(args.item[i]);
			args.item[i].itembriefEl = args.item[i].itembrief.el;
			//数量信息
			args.item[i].numchooser = new NumChooser({
				tip1:'数量：',
				tip2:'件',
				change:args.change,
				quantity:args.item[i].quantity,
			});
			args.item[i].numchooserEl = args.item[i].numchooser.el;
			//事件
			args.item[i].delClick = function(self){
				self = $(self).parent().parent();
				dialog.confirm('确定删除该商品？',function(){
					args.item.splice(i,1);
					self.remove();
					args.change();
				});
			}
			args.item[i].checkClick = function(self){
				self = $(self).find('.checked');
				self.toggleClass('active');
				args.change();
			}
		})(i);
	}
	args.id = _.uniqueId('common_itemlistwithnum_');
	var template = __inline('itemlistwithnumTpl.tpl');
	var el = template(args);
	function getChecked(){
		var datalist = [];
		var target = $('#'+args.id);
		for( var i = 0 ; i != args.item.length ; ++i ){
			singleTarget = target.find('li[data='+i+']'+' .checked');
			if( singleTarget.hasClass('active') == false )
				continue;
			args.item[i].quantity = args.item[i].numchooser.get();
			datalist.push( args.item[i] );
		}
		return datalist;
	}
	function getAll(){
		var datalist = [];
		var target = $('#'+args.id);
		for( var i = 0 ; i != args.item.length ; ++i ){
			args.item[i].quantity = args.item[i].numchooser.get();
			datalist.push( args.item[i] );
		}
		return datalist;
	}
	return {
		el:el,
		getChecked:getChecked,
		getAll:getAll
	}
}
return itemlistwithnum;