define('mobile/common/test/test.js', function(require, exports, module){

var $ = require('mobile/common/core/core.js');
function test(data){
	var el = '<div style="font-size:16px;color:red;text-align:center;">'+data+'</div>';
	return {
		el:el
	}
}
return test;

});