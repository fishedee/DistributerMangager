/*
*@require circlelist.less
*/
var $ = require('../core/core.js');
function create(args){
	var nowCircle = 0;
	function rotateNext(){
		var target = $('#common_circlelist');
		target.find('.item').removeClass('active');
		nowCircle = (nowCircle+1)%8;
		target.find('.item[index='+nowCircle+']').addClass('active');
	}
	function rotateEightLast(){
		var timer = 0;
		function next(){
			if( timer++ == 16 ){
				args.rotateFinish();
			}else{
				var interval = 100 + 50 * timer;
				rotateNext();
				setTimeout(next,interval);
			}
		}
		next();
	}
	function rotate(targetCircle){
		//先转50个圈
		nowCircle = -1;
		var timer = 0;
		interval = setInterval(function(){
			if( timer++ >= 30 && targetCircle == nowCircle ){
				clearInterval(interval);
				rotateEightLast();
			}else{
				rotateNext();
			}
		},100);
	}
	var template = __inline('circlelistTpl.tpl');
	var el = template(args);
	return {
		el:el,
		rotate:rotate
	}
}
return create;