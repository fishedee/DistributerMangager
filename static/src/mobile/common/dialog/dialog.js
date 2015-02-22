/*
*@require dialog.less
*/
var $ = require('/fishstrap/core/global.js');
var body = $('#body');
var loadingDiv = null;
function loadingBegin(){
	if( loadingDiv == null ){
		var template = __inline('dialogLoadingTpl.tpl');
		loadingDiv = $(template());
		body.append(loadingDiv);
	}
}
function loadingEnd(){
	if( loadingDiv != null ){
		loadingDiv.remove();
		loadingDiv = null;
	}
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