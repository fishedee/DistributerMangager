var ItemListWithNum = require('../../common/itemlistwithnum/itemlistwithnum.js');
function ItemListWithNumList(shopcart,change){
	//根据userId将数据分组
	var shopcartlists = {};
	for( var i in shopcart ){
		var single = shopcart[i];
		var userId = shopcart[i].userId;
		if( _.isUndefined(shopcartlists[userId]) == true ){
			shopcartlists[userId] = {};
			shopcartlists[userId].userId = userId;
			shopcartlists[userId].appName = single.appName;
			shopcartlists[userId].list = []; 
		}
		shopcartlists[userId].list.push({
			shopTrollerId:single.shopTrollerId,
			shopCommodityId:single.shopCommodityId,
			userId:single.userId,
			image:single.icon,
			title:single.title,
			summary:single.introduction,
			price:single.priceShow,
			stock:single.inventory,
			quantity:single.quantity,
		});
	}
	//建立多个itemlistwithnum
	for( var userId in shopcartlists ){
		var single = shopcartlists[userId];
		shopcartlists[userId].itemlistwithnum = new ItemListWithNum({
			title:'店铺：'+single.appName,
			item:single.list,
			change:change
		});
	}
	//输出el和get
	var el = '';
	for( var userId in shopcartlists ){
		var single = shopcartlists[userId];
		el += single.itemlistwithnum.el;
	}
	function getChecked(){
		return _.reduce(shopcartlists,function(meno,single){
			return meno.concat(single.itemlistwithnum.getChecked());
		},[]);
	}
	function getAll(){
		return _.reduce(shopcartlists,function(meno,single){
			return meno.concat(single.itemlistwithnum.getAll());
		},[]);
	}
	return {
		el:el,
		getChecked:getChecked,
		getAll:getAll
	}
}
return ItemListWithNumList;