/*
*@requrie metro.less
*/
var $ = require('../core/core.js');
function metro(args){
	var template = __inline('metroTpl.tpl');
	var colors = [
		{size:'33.3%',color:'rgb(54,170,231)'},
		{size:'33.3%',color:'rgb(104,140,226)'},
		{size:'33.3%',color:'rgb(141,103,224)'},
		{size:'50%',color:'rgb(132,208,24)'},
		{size:'50%',color:'rgb(20,199,97)'},
		{size:'33.3%',color:'rgb(244,181,18)'},
		{size:'33.3%',color:'rgb(251,140,69)'},
		{size:'33.3%',color:'rgb(252,83,102)'},
	];
	for( var i = 0 ; i != args.length ; ++i )
		args[i] = $.extend(args[i],colors[i%colors.length]);
	var el = template({list:args});
	return {
		el:el
	}
}
return metro;