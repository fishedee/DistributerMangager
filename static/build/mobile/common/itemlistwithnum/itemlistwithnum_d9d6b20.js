define('mobile/common/itemlistwithnum/itemlistwithnum.js', function(require, exports, module){

/*
*@require mobile/common/itemlistwithnum/itemlistwithnum.less
*/
var $ = require('mobile/common/core/core.js');
var dialog = require('mobile/common/dialog/dialog.js');
var ItemBrief = require('mobile/common/itembrief/itembrief.js');
var NumChooser = require('mobile/common/numchooser/numchooser.js');
function itemlistwithnum(args){
	for( var i = 0 ; i != args.item.length ; ++i ){
		//基本信息
		args.item[i].itembrief = new ItemBrief(args.item[i]);
		args.item[i].itembriefEl = args.item[i].itembrief.el;
		//数量信息
		args.item[i].numchooser = new NumChooser({
			tip1:'数量：',
			tip2:'件',
			quantity:args.item[i].quantity,
		});
		args.item[i].numchooserEl = args.item[i].numchooser.el;
		//事件
		args.item[i].delClick = function(self){
			self = $(self).parent().parent();
			dialog.confirm('确定删除该商品？',function(){
				self.remove();
			});
		}
		args.item[i].checkClick = function(self){
			self = $(self).find('.checked');
			self.toggleClass('active');
		}
		
	}
	args.id = _.uniqueId('common_itemlistwithnum_');
	var template = function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<div class="common_itemlistwithnum" id="'+
((__t=( id ))==null?'':_.escape(__t))+
'">\n\t<h2>'+
((__t=( title ))==null?'':_.escape(__t))+
'</h2>\n\t<ul class="items">\n\t\t';
 for( var i in item ){ 
__p+='\n\t\t\t<li data="'+
((__t=( i ))==null?'':_.escape(__t))+
'">\n\t\t\t\t<div class="info" onclick="'+
((__t=( $.func.invoke(item[i].checkClick) ))==null?'':__t)+
'">\n\t\t\t\t\t<div class="checked"></div>\n\t\t\t\t\t'+
((__t=( item[i].itembriefEl ))==null?'':__t)+
'\n\t\t\t\t</div>\n\t\t\t\t<div class="numinfo">\n\t\t\t\t\t<div class="del" onclick="'+
((__t=( $.func.invoke(item[i].delClick) ))==null?'':__t)+
'">删除</div>\n\t\t\t\t\t'+
((__t=( item[i].numchooserEl ))==null?'':__t)+
'\n\t\t\t\t</div>\n\t\t\t</li>\n\t\t';
 } 
__p+='\n\t</ul>\n</div>';
}
return __p;
};
	var el = template(args);
	function get(){
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
	return {
		el:el,
		get:get
	}
}
return itemlistwithnum;

});