/*
*@requrie footer.less
*/
var $ = require('../core/core.js');
function footer(choose,hasCompany){
	var data = [
		{
			link:'company.html',
			name:'company',
			state:'inactive',
			text:'公司'
		},
		{
			link:'item.html',
			name:'item',
			state:'inactive',
			text:'商品'
		},
		{
			link:'deal.html',
			name:'deal',
			state:'inactive',
			text:'订单'
		},
		{
			link:'me.html',
			name:'me',
			state:'inactive',
			text:'我'
		},
	];
	for( var i in data ){
		if( data[i].name == choose ){
			data[i].state = 'active';
		}
	}
	if( hasCompany == false )
		data.splice(0,1);
	var template = __inline('footerTpl.tpl');
	var el = template({list:data});
	return {
		el:el
	}
}
return footer;