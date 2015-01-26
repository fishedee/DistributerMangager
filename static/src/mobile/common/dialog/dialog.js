/*
*@requrie dialog.less
*/
var $ = require('/fishstrap/core/global.js');
var body = $('#body');
var loadingDiv;
function loadingBegin(){
	var template = __inline('dialogLoadingTpl.tpl');
	loadingDiv = $(template());
	body.append(loadingDiv);
}
function loadingEnd(){
	loadingDiv.remove();
}
function message(data){
	alert(data);
}
module.exports = {
	loadingBegin:loadingBegin,
	loadingEnd:loadingEnd,
	message:message
};