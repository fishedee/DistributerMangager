define('mobile/common/article/article.js', function(require, exports, module){

/*
*@requrie article.less
*/
var $ = require('mobile/common/core/core.js');
function replaceQQVideo(content,articleWith){
	var qqVideoRegex = /<embed.*?src="http:\/\/static.video.qq.com\/TPout.swf\?vid=(.*?)".*?width="(.*?)".*?height="(.*?)".*?\/>/img;
	var qqVideoWidth = articleWith;
	var qqVideoHeight = Math.ceil(qqVideoWidth /1.5);
	var qqVideoReplace = '<iframe class="video_iframe" style="position:relative; z-index:1;" height="'+qqVideoHeight+'px" width="'+qqVideoWidth+'px" frameborder="0" src="http://v.qq.com/iframe/player.html?vid=$1&width='+qqVideoWidth+'&height='+qqVideoHeight+'&auto=0" allowfullscreen=""></iframe>';
	content = content.replace(qqVideoRegex,qqVideoReplace);
	return content;
}
function replaceImage(content,articleWith){
	var imageRegex = /<img(.*?)\/>/ig;
	var imageReplace = '<img $1 style="width:'+articleWith+'px"/>';
	content = content.replace(imageRegex,imageReplace);
	return content;
}
function article(args){
	args.id = _.uniqueId('common_article_');
	
	//对ueditor生成的代码进行正则变换以适应手机页面
	var totalWidth = $('#body').width();
	var articleWith = totalWidth - 12*2;
	args.content = replaceQQVideo(args.content,articleWith);
	args.content = replaceImage(args.content,articleWith);

	var template = function(obj){
var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};
with(obj||{}){
__p+='<div id="common_article">\n\t<h1>'+
((__t=( title ))==null?'':_.escape(__t))+
'</h1>\n\t<div id="'+
((__t=( id ))==null?'':_.escape(__t))+
'">\n\t\t'+
((__t=( content ))==null?'':__t)+
'\n\t</div>\n</div>';
}
return __p;
};
	var el = template(args);
	return {
		el:el
	}
}
return article;

});