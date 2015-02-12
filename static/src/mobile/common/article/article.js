/*
*@requrie article.less
*/
var $ = require('../core/core.js');
var uedit = require('/fishstrap/util/uedit.js');
function article(args){
	args.id = _.uniqueId('common_article_');
	var totalWidth = $('#body').width();
	var articleWith = totalWidth - 12*2;
	args.content = uedit.parse(args.content,articleWith);

	var template = __inline('articleTpl.tpl');
	var el = template(args);
	return {
		el:el
	}
}
return article;