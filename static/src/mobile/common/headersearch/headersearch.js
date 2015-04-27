/*
*@requrie headersearch.less
*/
var $ = require('../core/core.js');
function headersearch(){
	var args = {
		click:function(){
			var target = $('#common_headersearch');
			var keyword = $.trim(target.find('input').val());
			if( keyword == ''){
				alert('请输入搜索关键词');
				return;
			}
			location.href = "search.html?keyword="+encodeURIComponent(keyword);
		}
	};
	function set(tip){
		var target = $('#common_headersearch');
		target.find('input').val(tip);
	}
	var template = __inline('headersearchTpl.tpl');
	var el = template(args);
	return {
		el:el,
		set:set
	}
}
return headersearch;