var $ = require('../core/core.js');

function appName(data) {
	var template = __inline('appNameTpl.tpl');
	var el = template(data);
	function set() {
		
	}
	return {
		el: el,
		set: set
	}
}
return appName;
