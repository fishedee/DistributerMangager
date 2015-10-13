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
function redPackRule(image,next){
	var template = __inline('redPackRuleTpl.tpl');
	var argv = {
		image:image,
		click:function(){
			var operation = {
				remove:function(){
					var target = $('#common_redPackRule');
					target.remove();
				}
			}
			next(operation);
		}
	};
	var div = template(argv);
	body.append(div);
}
function redPackShow(shop,num,nextClick,ruleClick){
	var template = __inline('redPackShowTpl.tpl');
	var argv = {
		image:__uri('redpackshow.jpg'),
		shop:shop,
		num:num,
		nextClick:function(){
			var operation = {
				remove:function(){
					var target = $('#common_redPackShow');
					target.remove();
				}
			}
			nextClick(operation);
		},
		ruleClick:function(){
			var operation = {
				remove:function(){
					var target = $('#common_redPackShow');
					target.remove();
				}
			}
			ruleClick(operation);
		}
	};
	var div = template(argv);
	body.append(div);
}
function redPackGet(num,next){
	var template = __inline('redPackGetTpl.tpl');
	var argv = {
		image:__uri('redpackget.jpg'),
		click:function(){
			var operation = {
				remove:function(){
					var target = $('#common_redPackGet');
					target.remove();
				}
			}
			next(operation);
		},
		num:num
	};
	var div = template(argv);
	body.append(div);

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
	inputInfo:inputInfo,
	redPackShow:redPackShow,
	redPackGet:redPackGet,
	redPackRule:redPackRule
};