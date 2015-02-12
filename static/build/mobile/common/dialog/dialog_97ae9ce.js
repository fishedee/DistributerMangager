define('mobile/common/dialog/dialog.js', function(require, exports, module){

/*
*@require mobile/common/dialog/dialog.less
*/
var $ = require('fishstrap/core/global.js');
var body = $('#body');
var loadingDiv;
function loadingBegin(){
	var template = function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<div id="common_dialog_loading">\n</div>';
}
return __p;
};
	loadingDiv = $(template());
	body.append(loadingDiv);
}
function loadingEnd(){
	loadingDiv.remove();
}
function message(text,next){
	alert(text);
	if( next )
		next();
}
function confirm(text,next){
	var result = window.confirm(text); 
	if( result )
		next();
}
module.exports = {
	loadingBegin:loadingBegin,
	loadingEnd:loadingEnd,
	message:message
};

});