define('mobile/common/itemarticle/itemarticle.js', function(require, exports, module){

/*
*@requrie itemarticle.less
*/
var $ = require('mobile/common/core/core.js');
var uedit = require('fishstrap/util/uedit.js');
var ItemBrief = require('mobile/common/itembrief/itembrief.js');
function itemarticle(args){
	args.itembrief = new ItemBrief(args).el;
	var totalWidth = $('#body').width();
	var articleWith = totalWidth - 12*2;
	args.content = uedit.parse(args.content,articleWith);

	var template = function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<div id="common_itemarticle">\n\t'+
((__t=( itembrief ))==null?'':__t)+
'\n\t<div class="content">'+
((__t=( content ))==null?'':__t)+
'</div>\n</div>';
}
return __p;
};
	var el = template(args);
	return {
		el:el
	}
}
return itemarticle;

});