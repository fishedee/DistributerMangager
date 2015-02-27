/*
*@require dialog.less
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
	message:message,
	confirm:confirm
};