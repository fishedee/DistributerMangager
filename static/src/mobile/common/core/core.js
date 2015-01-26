/*
*@require /fishstrap/lib/fastclick.js
*@require core.less
*/
var $ = require('/fishstrap/core/global.js');
var dialog = require('../dialog/dialog.js');
//加入FastClick扩展
window.FastClick.attach(document.body);
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
	$.page = {
		start:start,
		ready:ready
	};
}());
module.exports = $;