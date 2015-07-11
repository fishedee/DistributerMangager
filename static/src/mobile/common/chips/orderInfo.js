var $ = require('../core/core.js');

function orderInfo(data) {
	var list = new Object;
	list.firstpay = (data['newprice'] * data['percent'] * 0.01);
	list.firstpay =list.firstpay.toFixed(2);
	var template = __inline('orderInfoTpl.tpl');
	var el = template(list);
	function set() {

	}
	return {
		el: el,
		set: set
	}
}
return orderInfo;
