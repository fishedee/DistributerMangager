#创建数据库
drop database if exists DistributerManager;
create database DistributerManager;
use DistributerManager;

#创建ci的session表
create table ci_sessions (
  session_id char(40) default '0' not null,
  ip_address char(45) default '0' not null,
  user_agent char(120) not null,
  last_activity int(10) unsigned default 0 not null,
  user_data text not null,
  primary key (session_id),
  key `last_activity_idx` (`last_activity`)
);

#创建用户表
create table t_user(
	userId integer not null auto_increment,
	name varchar(32) not null,
	password char(60) not null,
	company varchar(128) not null,
	phone varchar(11) not null,
	type integer not null,
    downDistributionNum integer not null,
	createTime timestamp not null default CURRENT_TIMESTAMP,
	modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, 
	primary key( userId )
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

alter table t_user add index nameIndex(name,password);

#创建用户AppId与AppKey入口
create table t_user_app(
	userAppId integer not null auto_increment,
	userId integer not null,
	appName varchar(128) not null,
	appId varchar(128) not null,
	appKey varchar(128) not null,
	mchId varchar(128) not null,
	mchKey varchar(128) not null,
	mchSslCert varchar(128) not null,
	mchSslKey varchar(128) not null,
	remark varchar(128) not null,
	createTime timestamp not null default CURRENT_TIMESTAMP,
	modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, 
	primary key( userAppId )
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

alter table t_user_app add index userIdIndex(userId);

#创建客户表
create table t_client(
	clientId integer not null auto_increment,
	userId integer not null,
	openId varchar(128) not null,
	type integer not null,
	createTime timestamp not null default CURRENT_TIMESTAMP,
	modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, 
	primary key( clientId )
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

alter table t_client add index openIdIndex(openId);
alter table t_client add index userIdIndex(userId);

#创建地址表
create table t_address(
    addressId integer not null auto_increment,
    clientId integer not null,
    name varchar(32) not null,
    province varchar(32) not null,
    city varchar(32) not null,
    address varchar(128) not null,
    phone varchar(11) not null,
    payment integer not null,
    createTime timestamp not null default CURRENT_TIMESTAMP,
    modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    primary key(addressId)
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

alter table t_address add index clientIdIndex(clientId);

#创建用户权限表
create table t_user_permission(
	userPermissionId integer not null auto_increment,
	userId integer not null,
	permissionId integer not null,
	createTime timestamp not null default CURRENT_TIMESTAMP,
	modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, 
	primary key( userPermissionId )
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

alter table t_user_permission add index userIdIndex(userId);

#创建代理商的客户表
create table t_user_client(
	userClientId integer not null auto_increment,
	userId integer not null,
	clientUserId integer not null,
	createTime timestamp not null default CURRENT_TIMESTAMP,
	modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, 
	primary key( userClientId )
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

alter table t_user_client add index userIdIndex(userId);

#创建公司文章模板表
create table t_company_template(
	companyTemplateId integer not null auto_increment,
	title varchar(128) not null,
	url varchar(256) not null,
	remark varchar(256) not null,
	createTime timestamp not null default CURRENT_TIMESTAMP,
	modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, 
	primary key( companyTemplateId )
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

#创建用户公司模板表
create table t_user_company_template(
	userCompanyTemplateId integer not null auto_increment,
	userId integer not null,
	companyTemplateId integer not null,
	createTime timestamp not null default CURRENT_TIMESTAMP,
	modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, 
	primary key( userCompanyTemplateId )
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

alter table t_user_company_template add index userIdIndex(userId);


#创建用户公司文章分类表
create table t_user_company_classify(
	userCompanyClassifyId integer not null auto_increment,
	userId integer not null,
	title varchar(128) not null,
	icon varchar(128) not null,
	sort integer not null,
    link varchar(128) not null,
	remark varchar(128) not null,
	createTime timestamp not null default CURRENT_TIMESTAMP,
	modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, 
	primary key( userCompanyClassifyId )
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

alter table t_user_company_classify add index userIdIndex(userId);

#创建用户公司文章表
create table t_user_company_article(
	userCompanyArticleId integer not null auto_increment,
	userId integer not null,
	cover varchar(128) not null,
	title varchar(128) not null,
	remark varchar(256) not null,
	summary varchar(256) not null,
	content text not null,
	userCompanyClassifyId integer not null,
	createTime timestamp not null default CURRENT_TIMESTAMP,
	modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, 
	primary key( userCompanyArticleId )
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

alter table t_user_company_article add index userIdIndex(userId);

#创建用户公司广告表
create table t_user_company_banner(
	userCompanyBannerId integer not null auto_increment,
	userId integer not null,
	title varchar(256) not null,
	image varchar(256) not null,
	url varchar(256) not null,
	sort integer not null,
	createTime timestamp not null default CURRENT_TIMESTAMP,
	modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, 
	primary key( userCompanyBannerId )
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

alter table t_user_company_banner add index userIdIndex(userId);

#创建用户商城商品分类表
create table t_shop_commodity_classify(
    shopCommodityClassifyId integer not null auto_increment,
    userId integer not null,
    title varchar(128) not null,
    icon varchar(128) not null,
    parent integer not null,
    sort integer not null,
    link varchar(128) not null,
    remark varchar(128) not null,
    createTime timestamp not null default CURRENT_TIMESTAMP,
    modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    primary key( shopCommodityClassifyId )
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

alter table t_shop_commodity_classify add index userIdIndex(userId);

#创建用户商城商品表
create table t_shop_commodity(
    shopCommodityId integer not null auto_increment,
    userId integer not null,
    isLink integer not null,
    shopLinkCommodityId integer not null,#直接上级商品ID
    shopCommodityClassifyId integer not null,
    title varchar(128) not null,
    icon varchar(128) not null,
    introduction varchar(128) not null,
    detail text not null,
    price integer not null,
    oldPrice integer not null,
    inventory integer not null,
    state integer not null,
    remark varchar(128) not null,
    createTime timestamp not null default CURRENT_TIMESTAMP,
    modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    primary key(shopCommodityId)
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

alter table t_shop_commodity add index userIdIndex(userId);
alter table t_shop_commodity add index shopCommodityClassifyIdIndex(shopCommodityClassifyId);

#创建用户购物车表
create table t_shop_troller(
    shopTrollerId integer not null auto_increment,
    clientId integer not null,
    shopCommodityId integer not null,
    title varchar(128) not null,
    icon varchar(128) not null,
    introduction varchar(128) not null,
    price integer not null,
    oldPrice integer not null,
    quantity integer not null,
    createTime timestamp not null default CURRENT_TIMESTAMP,
	modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, 
    primary key(shopTrollerId)
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

alter table t_shop_troller add index clientIdIndex(clientId);

#创建用户订单表
create table t_shop_order(
	shopOrderId varchar(32) not null,
    userId integer not null,
    clientId integer not null,
    image varchar(128) not null,
    description varchar(128) not null,
   	price integer not null,
    num integer not null,
    name varchar(32) not null,
    wxPrePayId varchar(128) not null,
    state integer not null,
    remark varchar(128) not null,
    createTime timestamp not null default CURRENT_TIMESTAMP,
	modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, 
    primary key(shopOrderId)
)engine=innodb default charset=utf8mb4;

alter table t_shop_order add index matchIndex(userId, clientId);

#创建用户订单商品表
create table t_shop_order_commodity(
	shopOrderCommodityId integer not null auto_increment,
	shopOrderId varchar(32) not null,
	shopCommodityId integer not null,
    title varchar(128) not null,
    icon varchar(128) not null,
    introduction varchar(128) not null,
    price integer not null,
    oldPrice integer not null,
    quantity integer not null,
    createTime timestamp not null default CURRENT_TIMESTAMP,
	modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, 
    primary key(shopOrderCommodityId)
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

alter table t_shop_order_commodity add index shopOrderIdIndex(shopOrderId);

#创建用户订单地址表
create table t_shop_order_address(
	shopOrderAddressId integer not null auto_increment,
	shopOrderId varchar(32) not null,
	name varchar(32) not null,
    province varchar(32) not null,
    city varchar(32) not null,
    address varchar(128) not null,
    phone varchar(11) not null,
    payment integer not null,
    createTime timestamp not null default CURRENT_TIMESTAMP,
	modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, 
    primary key(shopOrderAddressId)
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

alter table t_shop_order_address add index shopOrderIdIndex(shopOrderId);


#创建分成关系表
create table t_distribution(
    distributionId integer not null auto_increment,
    upUserId integer not null,
    downUserId integer not null,
    shopUrl varchar(256) not null,
    distributionPercent integer not null,
    state integer not null,
    createTime timestamp not null default CURRENT_TIMESTAMP,
    modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    primary key(distributionId)
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

#创建分成订单表
create table t_distribution_order(
    distributionOrderId integer not null auto_increment,
    upUserId integer not null,
    downUserId integer not null,
    shopOrderId varchar(32) not null,
    price integer not null,
    state integer not null,
    createTime timestamp not null default CURRENT_TIMESTAMP,
    modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    primary key(distributionOrderId)
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

#创建分成订单商品表
create table t_distribution_commodity(
    distributionCommodityId  integer not null auto_increment,
    distributionOrderId integer not null,
    shopOrderId varchar(32) not null,
    shopCommodityId integer not null,
    price integer not null,
    createTime timestamp not null default CURRENT_TIMESTAMP,
    modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    primary key(distributionCommodityId)
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

#创建抽奖表
create table t_lucky_draw(
    luckyDrawId integer not null auto_increment,
    userId integer not null,
    title varchar(128) not null,
    summary varchar(2056) not null,
    state integer not null,
    beginTime timestamp not null,
    endTime timestamp not null,
    createTime timestamp not null default CURRENT_TIMESTAMP,
    modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    primary key(luckyDrawId)
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

alter table t_lucky_draw add index useIdIndex(userId);

#创建抽奖商品列表
create table t_lucky_draw_commodity(
    luckyDrawCommodityId integer not null auto_increment,
    luckyDrawId integer not null,
    title varchar(128) not null,
    image varchar(128) not null,
    type integer not null,
    quantity integer not null,
    precent integer not null,
    sort integer not null,
    createTime timestamp not null default CURRENT_TIMESTAMP,
    modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    primary key(luckyDrawCommodityId)
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

alter table t_lucky_draw_commodity add index luckyDrawIdIndex(luckyDrawId);

#创建抽奖的用户列表
create table t_lucky_draw_client(
    luckyDrawClientId integer not null auto_increment,
    luckyDrawId integer not null,
    clientId integer not null,
    title varchar(128) not null,
    image varchar(128) not null,
    type integer not null,
    name varchar(128) not null,
    phone varchar(11) not null,
    createTime timestamp not null default CURRENT_TIMESTAMP,
    modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    primary key(luckyDrawClientId)
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

alter table t_lucky_draw_client add index luckyDrawIdIndex(luckyDrawId);
alter table t_lucky_draw_client add index clientIdIndex(clientId);

#创建VIP设置
create table t_vip(
    vipId integer not null auto_increment,
    userId integer not null,
    cardImage varchar(128) not null,
    createTime timestamp not null default CURRENT_TIMESTAMP,
    modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    primary key(vipId)
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

alter table t_vip add index userIdIndex(userId);

#创建VIP用户设置
create table t_vip_client(
    vipClientId integer not null auto_increment,
    userId integer not null,
    clientId integer not null,
    name varchar(128) not null,
    phone varchar(11) not null, 
    score integer not null,
    createTime timestamp not null default CURRENT_TIMESTAMP,
    modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    primary key(vipClientId)
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

alter table t_vip_client add index userIdIndex(userId);
alter table t_vip_client add index clientIdIndex(clientId);

#建立初始数据
insert into t_user(userId,name,password,company,phone,type,downDistributionNum) values
(10001,"fish","$2y$10$xKsYkwOJFQo2Ack68DqZuebTX99IgHL0lYBKmpwQpkxqzhJbKYgMG",'烘焙帮信息科技有限公司','15018749403',1,0),
(10002,"fish_agent","$2y$10$xKsYkwOJFQo2Ack68DqZuebTX99IgHL0lYBKmpwQpkxqzhJbKYgMG",'烘焙帮信息科技有限公司','15018749403',2,0),
(10003,"fish_client","$2y$10$xKsYkwOJFQo2Ack68DqZuebTX99IgHL0lYBKmpwQpkxqzhJbKYgMG",'烘焙帮信息科技有限公司','15018749403',3,0),
(10004,"fish_client2","$2y$10$xKsYkwOJFQo2Ack68DqZuebTX99IgHL0lYBKmpwQpkxqzhJbKYgMG",'烘焙帮信息科技有限公司','15018749403',3,0),
(10005,"fish_client3","$2y$10$xKsYkwOJFQo2Ack68DqZuebTX99IgHL0lYBKmpwQpkxqzhJbKYgMG",'烘焙帮信息科技有限公司','15018749403',3,0),
(10006,"fish_client4","$2y$10$xKsYkwOJFQo2Ack68DqZuebTX99IgHL0lYBKmpwQpkxqzhJbKYgMG",'烘焙帮信息科技有限公司','15018749403',3,0);

insert into t_user_permission(userId,permissionId)values
(10003,1),
(10003,2),
(10003,3),
(10003,4),
(10004,1),
(10004,2),
(10004,3),
(10004,4),
(10005,1),
(10005,2),
(10005,3),
(10005,4),
(10006,1),
(10006,2),
(10006,3),
(10006,4);

insert into t_client(userId,openId,type)values
(10003,'微信测试用户虚拟OpenId',2),
(10004,'微信测试用户虚拟OpenId',2),
(10005,'微信测试用户虚拟OpenId',2),
(10006,'微信测试用户虚拟OpenId',2);

insert into t_address(clientId,name,province,city,address,phone,payment)values
(10001,'黎锦伟','广东','佛山','某地','15018749403',1);

insert into t_user_app(userId,appName,appId,appKey,mchId,mchKey,remark)values
(10003,'至高商城','wx5cc2d94dfe468c95','adc38d0974b0617023012fef684e9ae6','1220218001','56344f19b3b90eb545bf2f07800e7a10',''),
(10004,'至强商城','testAppId','testAppKey','testMchId','testMchKey','');

insert into t_company_template(title,url,remark)values
('metro风格','/data/upload/template/sample1',''),
('简约风格(测试用)','/data/upload/template/sample2','');

insert into t_user_company_template(userId,companyTemplateId)values
(10003,10001),
(10004,10001);

insert into t_user_company_classify(userId,title,icon,sort,remark)values
(10003,'行业新闻','/data/upload/sample/earth.png',1,''),
(10003,'公司文化','/data/upload/sample/cup.png',2,''),
(10003,'公司介绍','/data/upload/sample/home.png',3,''),
(10003,'数码产品','/data/upload/sample/picture.png',4,'');

insert into t_user_company_article(userId,cover,title,summary,remark,content,userCompanyClassifyId)values
(10003,'/data/upload/sample.jpg','文章标题1','文章简介1','','文章内容1',10001),
(10003,'/data/upload/sample.jpg','文章标题2','文章简介2','','文章内容2',10002),
(10003,'/data/upload/sample.jpg','文章标题3','文章简介3','','文章内容3',10003),
(10003,'/data/upload/sample.jpg','文章标题4','文章简介4','','文章内容4',10004),
(10003,'/data/upload/sample.jpg','文章标题5','文章简介5','','文章内容5',10001);

insert into t_user_company_banner(userId,image,title,url,sort)values
(10003,'/data/upload/sample/sample1.jpg','广告1','http://www.baidu.com',1),
(10003,'/data/upload/sample/sample2.jpg','广告2','http://www.qq.com',2),
(10003,'/data/upload/sample/sample3.jpg','广告3','http://www.sina.com',2);

insert into t_shop_commodity_classify(userId,title,icon,parent,sort,remark,link)values
(10003,'汽车','/data/upload/sample/sample1.jpg',0,1,'',''),
(10003,'宝马','/data/upload/sample/sample11.jpg',10001,2,'',''),
(10003,'大众','/data/upload/sample/sample12.jpg',10001,3,'',''),
(10003,'奥迪','/data/upload/sample/sample13.jpg',10001,4,'',''),
(10003,'饮食','/data/upload/sample/sample2.jpg',0,5,'',''),
(10003,'饮料','/data/upload/sample/sample11.jpg',10005,6,'',''),
(10003,'零食','/data/upload/sample/sample12.jpg',10005,7,'',''),
(10003,'主食','/data/upload/sample/sample13.jpg',10005,8,'',''),
(10004,'商品','/data/upload/sample/sample2.jpg',0,1,'',''),
(10003,'赢大奖抽iphone','/data/upload/sample/sample2.jpg',0,6,'','http://www.baidu.com');

insert into t_shop_commodity(userId,shopLinkCommodityId, isLink, shopCommodityClassifyId,icon,title,introduction,detail,price,oldPrice,inventory,state)values
(10003,0, 0, 10002,'/data/upload/sample/sample4.jpg','商品1','商品简介1','商品描述1',1,11100,10,1),
(10003,0, 0, 10003,'/data/upload/sample/sample5.jpg','商品2','商品简介2','商品描述2',2,22200,10,1),
(10003,0, 0, 10004,'/data/upload/sample/sample6.jpg','商品3','商品简介3','商品描述3',3,33300,10,1),
(10003,10005, 1, 10002,'','','','',1,1,1,1),
(10004,0, 0, 10009,'/data/upload/sample/sample6.jpg','商品4','商品简介4','商品描述4',4,44400,10,1),
(10004,10001, 1, 10009,'/data/upload/sample/sample6.jpg','商品4','商品简介4','商品描述4',4,44400,10,1);

insert into t_distribution(upUserId,downUserId,distributionPercent,shopUrl,state)values
(10003,10004,1234,'http://10003.shop.fishedee.com/10003/item.html',2),
(10004,10005,2345,'http://10003.shop.fishedee.com/10004/item.html',2),
(10005,10006,3456,'http://10003.fishedee.com/10006/item.html',2);

insert into t_shop_order(shopOrderId, userId, clientId, image, description, price, num, name, wxPrePayId, state, remark,createTime)values
(10001, 10003, 10001, '/data/upload/sample/sample4.jpg', '测试订单', 100, 3, '测试订单', '12323213', 1, '测试订单',now()),
(10002, 10004, 10001, '/data/upload/sample/sample6.jpg', '测试订单2', 90, 1, '测试订单2', '12323213', 1, '测试订单2',now()),
(10003, 10003, 10001, '/data/upload/sample/sample4.jpg', '测试订单', 80, 1, '测试订单3', '12323213', 1, '测试订单3',DATE_SUB(now(), INTERVAL 2 DAY)),
(10004, 10003, 10001, '/data/upload/sample/sample4.jpg', '测试订单', 70, 1, '测试订单4', '12323213', 1, '测试订单4',DATE_SUB(now(), INTERVAL 3 DAY));

insert into t_shop_order_address(shopOrderId, name, province, city, address, phone, payment)values
(10001, 'fish', '广东', '广州', '广州大学城', '15593728362', 1),
(10002, 'fish2', '广东', '广州', '广州大学城', '15593728362', 1),
(10003, 'fish', '广东', '广州', '广州大学城', '15593728362', 1),
(10004, 'fish', '广东', '广州', '广州大学城', '15593728362', 1);

insert into t_shop_order_commodity(shopOrderId, shopCommodityId, title, icon, introduction, price, OldPrice, quantity)values
(10001, 10001, '测试商品1', '/data/upload/sample/sample4.jpg', '测试商品1', 30, 500, 2),
(10001, 10002, '测试商品2', '/data/upload/sample/sample5.jpg', '测试商品2', 40, 500, 1),
(10002, 10003, '测试商品3', '/data/upload/sample/sample6.jpg', '测试商品3', 90, 500, 1),
(10003, 10001, '测试商品1', '/data/upload/sample/sample4.jpg', '测试商品1', 80, 500, 2),
(10004, 10002, '测试商品2', '/data/upload/sample/sample5.jpg', '测试商品2', 70, 500, 1);

insert into t_distribution_order(upUserId, downUserId, shopOrderId, price, state)values
(10003,10004, 10001, 0, 0),
(10004,10005, 10002, 0, 0);

insert into t_distribution_commodity(distributionOrderId, shopOrderId, shopCommodityId, price)values
(10001, 10001, 10001, 0),
(10001, 10001, 10002, 0),
(10002, 10002, 10003, 0);

insert into t_lucky_draw(userId,title,summary,state,beginTime,endTime)values
(10003,'开箱大抽奖','周一开始，持续到周日，五一停不了',2,now(),DATE_ADD(now(), INTERVAL 2 DAY));

insert into t_lucky_draw_commodity(luckyDrawId,title,image,type,quantity,precent,sort)values
(10001,'iphone1','/data/upload/sample/sample1.png',1,10,1000,1),
(10001,'iphone2','/data/upload/sample/sample2.png',1,10,1000,2),
(10001,'iphone3','/data/upload/sample/sample3.png',1,10,1000,3),
(10001,'iphone4','/data/upload/sample/sample4.png',1,10,1000,4),
(10001,'iphone5','/data/upload/sample/sample5.png',1,10,1000,5),
(10001,'iphone6','/data/upload/sample/sample6.png',1,10,1000,6),
(10001,'iphone7','/data/upload/sample/sample7.png',1,10,1000,7),
(10001,'iphone8','/data/upload/sample/sample8.png',2,10,3000,8);

#显示初始数据
select * from t_user;
select * from t_user_permission;
select * from t_user_client;
select * from t_user_app;
select * from t_company_template;
select * from t_user_company_template;
select * from t_user_company_template;
select * from t_user_company_classify;
select * from t_user_company_article;
select * from t_user_company_banner;
select * from t_shop_commodity_classify;
select * from t_shop_commodity;
select * from t_shop_order;
select * from t_shop_order_address;
select * from t_shop_order_commodity;
select * from t_distribution_order;
select * from t_distribution_commodity;
select * from t_distribution;