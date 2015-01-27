/*
*@requrie article.less
*/
var $ = require('../core/core.js');
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

	var template = __inline('articleTpl.tpl');
	var el = template(args);
	return {
		el:el
	}
}
return article;