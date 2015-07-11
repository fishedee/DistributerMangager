/*
 *@require swiper.css
 */

var $ = require('../core/core.js');
var Swiper = require('swiper.js');

function banner(data) {
	var template = __inline('bannerTpl.tpl');
	var el = template({list:data});
	function set() {
		var swiper = new Swiper('.swiper-container');
	}
	return {
		el: el,
		set: set
	}
}
return banner;