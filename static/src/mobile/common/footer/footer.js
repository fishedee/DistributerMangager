/*
*@requrie footer.less
*/
var $ = require('../core/core.js');
function footer(choose){
	var data = {
		companyState:'inactive',
		itemState:'inactive',
		dealState:'inactive',
		meState:'inactive'
	};
	data[choose+'State'] = 'active';
	var template = __inline('footerTpl.tpl');
	var el = template(data);
	return {
		el:el
	}
}
return footer;