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
var inputDiv = null;
function inputInfo(name,phone,next){
	if( inputDiv != null )
		return;
	var args = {
		name:name,
		phone:phone,
		confirmClick:function(){
			var target = $('#common_dialog_input');
			var name = target.find('input[name=name]').val();
			var phone = target.find('input[name=phone]').val();
			if( name == '' || phone == ''){
				alert('请输入名字和手机噢');
				return;
			}
			inputDiv.remove();
			inputDiv = null;
			next({
				name:name,
				phone:phone
			});
		},
		cancelClick:function(){
			inputDiv.remove();
			inputDiv = null;
		}
	}
	var template = __inline('dialogInputTpl.tpl');
	inputDiv = $(template(args));
	body.append(inputDiv);
}
module.exports = {
	loadingBegin:loadingBegin,
	loadingEnd:loadingEnd,
	message:message,
	confirm:confirm,
	inputInfo:inputInfo
};