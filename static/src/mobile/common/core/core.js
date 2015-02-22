/*
*@require /fishstrap/lib/fastclick.js
*@require core.less
*/
var $ = require('/fishstrap/core/global.js');
var dialog = require('../dialog/dialog.js');
//加入FastClick扩展
window.FastClick.attach(document.body);
//从当前url分析出userId
function getUserIdFromUrl(){
	var userIdRegex = /http:\/\/(.*)?\/([0-9]+)/;
	var userIdMatch = location.href.match(userIdRegex);
	if( userIdMatch == null )
		return null;
	return userIdMatch[2];
}
//修改ajax函数，加入自动转菊花，自动转换json，自动捕捉json错误和网络错误的功能。
var _dialogAjax = $.ajax;
$.ajax = function(opt){
	dialog.loadingBegin();
	var tempComplete = opt.complete;
	opt.complete = function(XMLHttpRequest,textStatus){
		dialog.loadingEnd();
		if( tempComplete )
			tempComplete(XMLHttpRequest,textStatus);
	}
	var tempSuccess = opt.success;
	opt.success = function(data){
		try{
			data = $.JSON.parse(data);
			$.log.debug(data);
		}catch(e){
			data = {
				'code':1,
				'msg':'JSON解析错误',
				'data':''
			};
		}
		tempSuccess(data);
	}
	var tempError = opt.error;
	opt.error = function(XMLHttpRequest, textStatus, errorThrown){
		data = {
			'code':1,
			'msg':'',
			'data':''
		};
		data.msg = '网络错误，请稍后再试，网络错误码'+XMLHttpRequest.status;
		tempSuccess(data);
		if( tempError )
			tempError(XMLHttpRequest, textStatus, errorThrown);
	}
	var userId = getUserIdFromUrl();
	if( userId == null ){
		tempSuccess({
			'code':1,
			'msg':'缺少userId参数',
			'data':''
		});
		return;
	}
	opt.data = $.extend(opt.data,{userId:userId});
	_dialogAjax(opt);
};
//单页面入口
(function(){
	var body = $('#body');
	var readyFun = [];
	function start(html){
		body.empty();
		body.append(html);
		for( var i in readyFun ){
			readyFun[i]();
		}
		readyFun = [];
	}
	function ready(fun){
		readyFun.push(fun);
	}
	function append(html){
		body.append(html);
	}
	function setGrey(){
		$('body').addClass('body_grey');
	}
	$.page = {
		start:start,
		append:append,
		ready:ready,
		setGrey:setGrey
	};
}());
//函数空间
(function(){
	window.funcArray = {};
	function invoke(fun){
		var funcUniqueId = _.uniqueId('func_');
		window.funcArray[funcUniqueId] = fun;
		return "window.funcArray['"+funcUniqueId+"'](this)";
	}
	function clear(){
		window.funcArray = {};
	}
	$.func = {
		invoke:invoke,
		clear:clear
	};
})();
//检查登录态并自动跳转
(function(){
	 function checkMustLogin(next){
	 	$.get('/clientlogin/islogin',{},function(data){
	 		if( data.code != 0 ){
	 			location.href = $.url.buildQueryUrl('/clientlogin/wxlogin',{callback:location.href,userId:getUserIdFromUrl()});
	 			return;
	 		}
	 		next();
	 	});
	}
	$.checkMustLogin = checkMustLogin;
})();
module.exports = $;