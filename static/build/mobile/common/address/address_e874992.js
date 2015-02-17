define('mobile/common/address/address.js', function(require, exports, module){

/*
*@requrie address.less
*/
var me_district_distruct = [
["北京","东城|西城|崇文|宣武|朝阳|丰台|石景山|海淀|门头沟|房山|通州|顺义|昌平|大兴|平谷|怀柔|密云|延庆"],    
["上海","黄浦|卢湾|徐汇|长宁|静安|普陀|闸北|虹口|杨浦|闵行|宝山|嘉定|浦东|金山|松江|青浦|南汇|奉贤|崇明"],    
["天津","和平|东丽|河东|西青|河西|津南|南开|北辰|河北|武清|红挢|塘沽|汉沽|大港|宁河|静海|宝坻|蓟县"],    
["重庆","万州|涪陵|渝中|大渡口|江北|沙坪坝|九龙坡|南岸|北碚|万盛|双挢|渝北|巴南|黔江|长寿|綦江|潼南|铜梁|大足|荣昌|壁山|梁平|城口|丰都|垫江|武隆|忠县|开县|云阳|奉节|巫山|巫溪|石柱|秀山|酉阳|彭水|江津|合川|永川|南川"],    
["河北","石家庄|邯郸|邢台|保定|张家口|承德|廊坊|唐山|秦皇岛|沧州|衡水"],    
["山西","太原|大同|阳泉|长治|晋城|朔州|吕梁|忻州|晋中|临汾|运城"],    
["内蒙古","呼和浩特|包头|乌海|赤峰|呼伦贝尔盟|阿拉善盟|哲里木盟|兴安盟|乌兰察布盟|锡林郭勒盟|巴彦淖尔盟|伊克昭盟"],    
["辽宁","沈阳|大连|鞍山|抚顺|本溪|丹东|锦州|营口|阜新|辽阳|盘锦|铁岭|朝阳|葫芦岛"],    
["吉林","长春|吉林|四平|辽源|通化|白山|松原|白城|延边"],    
["黑龙江","哈尔滨|齐齐哈尔|牡丹江|佳木斯|大庆|绥化|鹤岗|鸡西|黑河|双鸭山|伊春|七台河|大兴安岭"],    
["江苏","南京|镇江|苏州|南通|扬州|盐城|徐州|连云港|常州|无锡|宿迁|泰州|淮安"],    
["浙江","杭州|宁波|温州|嘉兴|湖州|绍兴|金华|衢州|舟山|台州|丽水"],    
["安徽","合肥|芜湖|蚌埠|马鞍山|淮北|铜陵|安庆|黄山|滁州|宿州|池州|淮南|巢湖|阜阳|六安|宣城|亳州"],    
["福建","福州|厦门|莆田|三明|泉州|漳州|南平|龙岩|宁德"],    
["江西","南昌市|景德镇|九江|鹰潭|萍乡|新馀|赣州|吉安|宜春|抚州|上饶"],    
["山东","济南|青岛|淄博|枣庄|东营|烟台|潍坊|济宁|泰安|威海|日照|莱芜|临沂|德州|聊城|滨州|菏泽"],    
["河南","郑州|开封|洛阳|平顶山|安阳|鹤壁|新乡|焦作|濮阳|许昌|漯河|三门峡|南阳|商丘|信阳|周口|驻马店|济源"],    
["湖北","武汉|宜昌|荆州|襄樊|黄石|荆门|黄冈|十堰|恩施|潜江|天门|仙桃|随州|咸宁|孝感|鄂州"],  
["湖南","长沙|常德|株洲|湘潭|衡阳|岳阳|邵阳|益阳|娄底|怀化|郴州|永州|湘西|张家界"],    
["广东","广州|深圳|珠海|汕头|东莞|中山|佛山|韶关|江门|湛江|茂名|肇庆|惠州|梅州|汕尾|河源|阳江|清远|潮州|揭阳|云浮"],    
["广西","南宁|柳州|桂林|梧州|北海|防城港|钦州|贵港|玉林|南宁地区|柳州地区|贺州|百色|河池"],    
["海南","海口|三亚"],    
["四川","成都|绵阳|德阳|自贡|攀枝花|广元|内江|乐山|南充|宜宾|广安|达川|雅安|眉山|甘孜|凉山|泸州"],    
["贵州","贵阳|六盘水|遵义|安顺|铜仁|黔西南|毕节|黔东南|黔南"],    
["云南","昆明|大理|曲靖|玉溪|昭通|楚雄|红河|文山|思茅|西双版纳|保山|德宏|丽江|怒江|迪庆|临沧"],  
["西藏","拉萨|日喀则|山南|林芝|昌都|阿里|那曲"],    
["陕西","西安|宝鸡|咸阳|铜川|渭南|延安|榆林|汉中|安康|商洛"],    
["甘肃","兰州|嘉峪关|金昌|白银|天水|酒泉|张掖|武威|定西|陇南|平凉|庆阳|临夏|甘南"],    
["宁夏","银川|石嘴山|吴忠|固原"], 
["青海","西宁|海东|海南|海北|黄南|玉树|果洛|海西"],    
["新疆","乌鲁木齐|石河子|克拉玛依|伊犁|巴音郭勒|昌吉|克孜勒苏柯尔克孜|博尔塔拉|吐鲁番|哈密|喀什|和田|阿克苏"],
];
function getAllProvince(){
	return _.map(me_district_distruct,function(single){
		return single[0];
	});
}
function getAllCity(province){
	var provinceInfo =  _.find(me_district_distruct,function(single){
		return single[0] == province;
	});
	if( _.isUndefined(provinceInfo) )
		return [];
	return provinceInfo[1].split("|");
}
function getAllPayment(){
	return ['微信支付','货到付款'];
}
var $ = require('mobile/common/core/core.js');
var SelectList = require('mobile/common/selectlist/selectlist.js');

function address(args){
	console.log(args);
	//设置默认参数
	var tempArgs = {
		name:'',
		province:'广东',
		city:'佛山',
		address:'',
		phone:'',
		payment:'微信支付',
	};
	args = $.extend(tempArgs,args);
	//设置下拉列表
	args.provinceSelectList = new SelectList({
		change:whenChangeProvince,
		value:args.province,
		list:_.map(getAllProvince(),function(single){
			return {name:single,value:single};
		})
	});
	args.provinceSelectListEl = args.provinceSelectList.el;
	args.citySelectList = new SelectList({
		change:_.noop,
		value:args.city,
		list:_.map(getAllCity(args.province),function(single){
			return {name:single,value:single};
		})
	});
	args.citySelectListEl = args.citySelectList.el;
	args.paymentSelectList = new SelectList({
		change:_.noop,
		value:args.payment,
		list:_.map(getAllPayment(),function(single){
			return {name:single,value:single};
		})
	});
	args.paymentSelectListEl = args.paymentSelectList.el;
	//生成页面
	var template = function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<ul id="common_address">\n\t<li class="name">\n\t\t<span>姓名：</span>\n\t\t<div class="input"><input type="text" value="'+
((__t=( name ))==null?'':_.escape(__t))+
'"></div>\n\t</li>\n\t<li class="province">\n\t\t<span>省份：</span>\n\t\t<div class="input">'+
((__t=( provinceSelectListEl ))==null?'':__t)+
'</div>\n\t</li>\n\t<li class="city">\n\t\t<span>城市：</span>\n\t\t<div class="input">'+
((__t=( citySelectListEl ))==null?'':__t)+
'</div>\n\t</li>\n\t<li class="address">\n\t\t<span>地址：</span>\n\t\t<div class="input"><input type="text" value="'+
((__t=( address ))==null?'':_.escape(__t))+
'"></div>\n\t</li>\n\t<li class="phone">\n\t\t<span>手机号码：</span>\n\t\t<div class="input"><input type="text" value="'+
((__t=( phone ))==null?'':_.escape(__t))+
'"></div>\n\t</li>\n\t<li class="payment">\n\t\t<span>支付方式：</span>\n\t\t<div class="input">'+
((__t=( paymentSelectListEl ))==null?'':__t)+
'</div>\n\t</li>\n</ul>';
}
return __p;
};
	var el = template(args);
	function whenChangeProvince(){
		var citys = getAllCity(args.provinceSelectList.get());
		args.citySelectList.set({
			change:_.noop,
			value:'',
			list:_.map(citys,function(single){
				return {name:single,value:single};
			})
		});
	}
	function get(){
		var target = $('#common_address');
		var data = {
			name:target.find('.name input').val(),
			province:args.provinceSelectList.get(),
			city:args.citySelectList.get(),
			address:target.find('.address input').val(),
			phone:target.find('.phone input').val(),
			payment:args.paymentSelectList.get()
		};
		return data;
	}
	return {
		el:el,
		get:get
	}
}
return address;

});