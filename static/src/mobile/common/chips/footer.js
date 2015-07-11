/*
*@require ../footer/footer.less
*/
var $ = require('../core/core.js');
function footer(){
	var template = __inline('footerTpl.tpl');
	var el = template();
	function set(){
	$('#common_header').on('click', '.backicon', function (event) {
			history.go(-1);
		});
	}
	return {
		el:el
	}
}
return footer;