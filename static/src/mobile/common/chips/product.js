var $ = require('../core/core.js');

function product(data) {
	var template = __inline('productTpl.tpl');
	var el = template({list:data});
	function set() {
		$(".chips_start[start=0]").css('background', '#272822');
	}
	return {
		el: el,
		set: set
	}
}
return product;
