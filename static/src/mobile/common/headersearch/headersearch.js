/*
*@requrie headersearch.less
*/
var $ = require('../core/core.js');
function headersearch(){
	var args = {
		click:function(){
			location.href = "search.html";
		}
	};
	var template = __inline('headersearchTpl.tpl');
	var el = template(args);
	return {
		el:el
	}
}
return headersearch;