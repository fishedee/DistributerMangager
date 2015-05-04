/*
*@require /fishstrap/lib/fastclick.js
*@require core.less
*/
var $ = require('/fishstrap/core/global.js');
var dialog = require('../dialog/dialog.js');
//加入FastClick扩展
window.FastClick.attach(document.body);
//检查登录态并自动跳转
(function(){
	var clientId = 1;
	function checkMustLogin(next){
	 	$.get('/clientlogin/islogin',{},function(data){
	 		if( data.code != 0 ){
	 			location.href = $.url.buildQueryUrl('/clientlogin/wxlogin',{callback:location.href});
	 			return;
	 		}
	 		clientId = data.data;
	 		next();
	 	});
	}
	function getTopDomain(){
		var host = location.host;
		return host.substr(host.indexOf('.')+1);
	}
	function getEntranceUserId(){
		var path = location.pathname;
		return path.split('/')[1];
	}
	function getClientId(){
		return clientId;
	}
	function getUserId(){
		var host = location.host;
		return host.substr(0,host.indexOf('.'));
	}
	$.checkMustLogin = checkMustLogin;
	$.getTopDomain = getTopDomain;
	$.getClientId = getClientId;
	$.getEntranceUserId = getEntranceUserId;
	$.getUserId = getUserId;
})();
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
	function setRed(){
		$('body').addClass('body_red');
	}
	$.page = {
		start:start,
		append:append,
		ready:ready,
		setGrey:setGrey,
		setRed:setRed
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
//加入红包校验逻辑
$.page.ready(function(){
	$.get('/redpack/getRedPack',{},function(data){
		if( data.code != 0 )
			return;
		dialog.redPackOne(function(){
			$.get('/redpack/tryRedPack',{},function(data){
				if( data.code != 0 ){
					dialog.message(data.msg);
					return;
				}
				dialog.redPackTwo('xx商城','100.00');
			});
		});
	});
});
module.exports = $;