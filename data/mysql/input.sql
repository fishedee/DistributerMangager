SET FOREIGN_KEY_CHECKS = 0;

#创建ci的session表
DROP TABLE IF EXISTS  `ci_sessions`;
CREATE TABLE `ci_sessions` (
  `id` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#创建活动管理表
DROP TABLE IF EXISTS  `t_activity`;
CREATE TABLE `t_activity` (
  `activityId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `clientId` int(11) DEFAULT NULL,
  `name` varchar(32) NOT NULL,
  `phone` varchar(32) NOT NULL,
  `sex` varchar(4) NOT NULL,
  `address` varchar(128) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`activityId`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8;

#创建收货人地址表
DROP TABLE IF EXISTS  `t_address`;
CREATE TABLE `t_address` (
  `addressId` int(11) NOT NULL AUTO_INCREMENT,
  `clientId` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `province` varchar(32) NOT NULL,
  `city` varchar(32) NOT NULL,
  `address` varchar(128) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `payment` int(11) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`addressId`),
  KEY `clientIdIndex` (`clientId`)
) ENGINE=InnoDB AUTO_INCREMENT=10540 DEFAULT CHARSET=utf8mb4;

#创建众筹商品表
DROP TABLE IF EXISTS  `t_chips`;
CREATE TABLE `t_chips` (
  `chips_id` int(10) NOT NULL AUTO_INCREMENT,
  `product_title` varchar(50) NOT NULL,
  `oldprice` double NOT NULL,
  `newprice` double NOT NULL,
  `base` double NOT NULL,
  `num` int(10) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `create_time` varchar(100) NOT NULL,
  `start_time` varchar(100) NOT NULL,
  `end_time` varchar(100) NOT NULL,
  `percent` double NOT NULL,
  `down_num` int(10) NOT NULL,
  `down_price` double NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `detail` text NOT NULL,
  `remark` varchar(255) NOT NULL,
  `is_delete` tinyint(1) DEFAULT '0',
  `stock` int(10) NOT NULL,
  `userId` int(10) NOT NULL,
  `start` int(1) NOT NULL,
  `password` varchar(32) NOT NULL,
  PRIMARY KEY (`chips_id`),
  KEY `chipsIdIndex` (`chips_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10004 DEFAULT CHARSET=utf8mb4;

#创建众筹轮播图表
DROP TABLE IF EXISTS  `t_chips_banner`;
CREATE TABLE `t_chips_banner` (
  `chips_banner_id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `img` varchar(255) NOT NULL,
  `url` varchar(128) NOT NULL,
  `title` varchar(128) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`chips_banner_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10002 DEFAULT CHARSET=utf8mb4;

#创建众筹联系方式表
DROP TABLE IF EXISTS  `t_chips_contract`;
CREATE TABLE `t_chips_contract` (
  `chips_contract_id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `phone` varchar(32) NOT NULL,
  PRIMARY KEY (`chips_contract_id`),
  UNIQUE KEY `userId` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4;

#创建众筹订单表
DROP TABLE IF EXISTS  `t_chips_order`;
CREATE TABLE `t_chips_order` (
  `chips_order_id` int(11) NOT NULL AUTO_INCREMENT,
  `orderNo` varchar(32) NOT NULL,
  `userId` int(11) NOT NULL,
  `clientId` int(11) NOT NULL,
  `chips_id` int(11) NOT NULL,
  `num` int(11) NOT NULL,
  `percent` double NOT NULL,
  `firstpay` double NOT NULL,
  `unit_price` double NOT NULL,
  `time` int(10) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `addressId` int(11) NOT NULL,
  `wxPrePayId` varchar(128) NOT NULL,
  `wxPrePayId2` varchar(128) NOT NULL,
  `name` varchar(32) NOT NULL,
  `province` varchar(32) NOT NULL,
  `city` varchar(32) NOT NULL,
  `address` varchar(128) NOT NULL,
  `phone` int(11) NOT NULL,
  `end_free` double NOT NULL,
  `end_unit_price` double NOT NULL,
  `down_time` varchar(128) NOT NULL,
  `pay_first_time` varchar(128) NOT NULL,
  `pay_all_time` varchar(128) NOT NULL,
  PRIMARY KEY (`chips_order_id`),
  UNIQUE KEY `orderNo` (`orderNo`),
  KEY `chipsOrderIdIndex` (`chips_order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10006 DEFAULT CHARSET=utf8mb4;

#创建众筹权限表
DROP TABLE IF EXISTS  `t_chips_power`;
CREATE TABLE `t_chips_power` (
  `powerId` int(11) NOT NULL AUTO_INCREMENT,
  `clientId` int(11) NOT NULL,
  `chips_id` int(11) NOT NULL,
  PRIMARY KEY (`powerId`)
) ENGINE=InnoDB AUTO_INCREMENT=10010 DEFAULT CHARSET=utf8mb4;

#创建众筹记录表
DROP TABLE IF EXISTS  `t_chips_record`;
CREATE TABLE `t_chips_record` (
  `chips_record_id` int(11) NOT NULL AUTO_INCREMENT,
  `clientId` int(11) NOT NULL,
  `chips_id` int(11) NOT NULL,
  `newprice` double NOT NULL,
  `num` int(11) NOT NULL,
  PRIMARY KEY (`chips_record_id`),
  KEY `chipsRecordIdIndex` (`chips_record_id`),
  KEY `chipsPowerClientId` (`clientId`)
) ENGINE=InnoDB AUTO_INCREMENT=10004 DEFAULT CHARSET=utf8mb4;

#创建微信用户表
DROP TABLE IF EXISTS  `t_client`;
CREATE TABLE `t_client` (
  `clientId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `openId` varchar(128) NOT NULL,
  `headImgUrl` varchar(255) NOT NULL,
  `nickName` varchar(255) NOT NULL,
  `type` int(11) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `subscribe` int(1) NOT NULL,
  `score` int(11) NOT NULL DEFAULT '0',
  `sales` int(11) NOT NULL DEFAULT '0',
  `fall` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`clientId`),
  KEY `openIdIndex` (`openId`),
  KEY `userIdIndex` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=12111 DEFAULT CHARSET=utf8mb4;

#常见公司模板表
DROP TABLE IF EXISTS  `t_company_template`;
CREATE TABLE `t_company_template` (
  `companyTemplateId` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `url` varchar(256) NOT NULL,
  `remark` varchar(256) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `type` int(11) NOT NULL,
  `defaultTemplate` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`companyTemplateId`)
) ENGINE=InnoDB AUTO_INCREMENT=10026 DEFAULT CHARSET=utf8mb4;

#创建模板权限表
DROP TABLE IF EXISTS  `t_company_template_power`;
CREATE TABLE `t_company_template_power` (
  `powerId` int(11) NOT NULL AUTO_INCREMENT,
  `companyTemplateId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  PRIMARY KEY (`powerId`)
) ENGINE=InnoDB AUTO_INCREMENT=10019 DEFAULT CHARSET=utf8mb4;

#创建招商加盟表
DROP TABLE IF EXISTS  `t_cooperation`;
CREATE TABLE `t_cooperation` (
  `cooperationId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `business_name` varchar(50) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `contract` varchar(50) NOT NULL,
  `province` varchar(50) NOT NULL,
  `city` varchar(50) NOT NULL,
  `newlocation` varchar(50) NOT NULL,
  `will` text NOT NULL,
  PRIMARY KEY (`cooperationId`),
  KEY `cooperationUserIdIndex` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=10011 DEFAULT CHARSET=utf8mb4;

#创建卡券表
DROP TABLE IF EXISTS  `t_coupons`;
CREATE TABLE `t_coupons` (
  `couponsId` int(11) NOT NULL AUTO_INCREMENT,
  `card_id` varchar(255) NOT NULL,
  `userId` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  PRIMARY KEY (`couponsId`),
  KEY `couponsUserId` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4;

#创建分成关系表
DROP TABLE IF EXISTS  `t_distribution`;
CREATE TABLE `t_distribution` (
  `distributionId` int(11) NOT NULL AUTO_INCREMENT,
  `upUserId` int(11) NOT NULL,
  `downUserId` int(11) NOT NULL,
  `shopUrl` varchar(256) NOT NULL,
  `distributionPercent` int(11) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `state` int(11) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `remark` varchar(255) NOT NULL,
  `scort` int(11) NOT NULL,
  `line` int(11) NOT NULL DEFAULT '1',
  `vender` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `recommend` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`distributionId`)
) ENGINE=InnoDB AUTO_INCREMENT=10049 DEFAULT CHARSET=utf8mb4;

#创建分成订单订单商品表
DROP TABLE IF EXISTS  `t_distribution_commodity`;
CREATE TABLE `t_distribution_commodity` (
  `distributionCommodityId` int(11) NOT NULL AUTO_INCREMENT,
  `distributionOrderId` int(11) NOT NULL,
  `shopOrderId` varchar(32) NOT NULL,
  `shopCommodityId` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`distributionCommodityId`)
) ENGINE=InnoDB AUTO_INCREMENT=10025 DEFAULT CHARSET=utf8mb4;

#创建分成订单表
DROP TABLE IF EXISTS  `t_distribution_order`;
CREATE TABLE `t_distribution_order` (
  `distributionOrderId` int(11) NOT NULL AUTO_INCREMENT,
  `upUserId` int(11) NOT NULL,
  `downUserId` int(11) NOT NULL,
  `shopOrderId` varchar(32) NOT NULL,
  `price` int(11) NOT NULL,
  `state` int(11) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `distributionId` int(11) NOT NULL,
  `vender` int(11) NOT NULL,
  `entranceUserId` int(11) NOT NULL,
  PRIMARY KEY (`distributionOrderId`)
) ENGINE=InnoDB AUTO_INCREMENT=10023 DEFAULT CHARSET=utf8mb4;

#创建抽奖表
DROP TABLE IF EXISTS  `t_lucky_draw`;
CREATE TABLE `t_lucky_draw` (
  `luckyDrawId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `method` int(1) NOT NULL DEFAULT '1',
  `summary` varchar(2056) NOT NULL,
  `state` int(11) NOT NULL,
  `beginTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `endTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`luckyDrawId`),
  KEY `useIdIndex` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=10023 DEFAULT CHARSET=utf8mb4;

#创建抽奖的用户列表
DROP TABLE IF EXISTS  `t_lucky_draw_client`;
CREATE TABLE `t_lucky_draw_client` (
  `luckyDrawClientId` int(11) NOT NULL AUTO_INCREMENT,
  `luckyDrawId` int(11) NOT NULL,
  `clientId` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `image` varchar(128) NOT NULL,
  `type` int(11) NOT NULL,
  `name` varchar(128) DEFAULT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `card_id` varchar(125) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`luckyDrawClientId`),
  KEY `luckyDrawIdIndex` (`luckyDrawId`),
  KEY `clientIdIndex` (`clientId`)
) ENGINE=InnoDB AUTO_INCREMENT=10401 DEFAULT CHARSET=utf8mb4;

#创建抽奖商品列表
DROP TABLE IF EXISTS  `t_lucky_draw_commodity`;
CREATE TABLE `t_lucky_draw_commodity` (
  `luckyDrawCommodityId` int(11) NOT NULL AUTO_INCREMENT,
  `luckyDrawId` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `image` varchar(128) NOT NULL,
  `type` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `coupon_id` int(11) DEFAULT '0',
  `card_id` varchar(125) DEFAULT NULL,
  `precent` int(11) NOT NULL,
  `sort` int(11) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`luckyDrawCommodityId`),
  KEY `luckyDrawIdIndex` (`luckyDrawId`)
) ENGINE=InnoDB AUTO_INCREMENT=10496 DEFAULT CHARSET=utf8mb4;

#创建会员卡表
DROP TABLE IF EXISTS  `t_member_card`;
CREATE TABLE `t_member_card` (
  `memberCardId` int(11) NOT NULL AUTO_INCREMENT,
  `card_id` varchar(125) NOT NULL,
  `userId` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT 'CARD_STATUS_NOT_VERIFY',
  `num` int(11) NOT NULL,
  `defaultCard` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`memberCardId`),
  KEY `memberCardUserId` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=10002 DEFAULT CHARSET=utf8mb4;

#创建账户明细表
DROP TABLE IF EXISTS  `t_money_log`;
CREATE TABLE `t_money_log` (
  `moneyLogId` int(11) NOT NULL AUTO_INCREMENT,
  `vender` int(11) NOT NULL,
  `clientId` int(11) NOT NULL,
  `money` int(11) NOT NULL,
  `dis` tinyint(1) NOT NULL DEFAULT '1',
  `remark` varchar(32) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`moneyLogId`),
  KEY `moneyLogIdIndex` (`moneyLogId`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4;

#创建已兑换商品表
DROP TABLE IF EXISTS  `t_points_order`;
CREATE TABLE `t_points_order` (
  `orderId` int(11) NOT NULL AUTO_INCREMENT,
  `productName` varchar(255) NOT NULL,
  `productImg` varchar(255) DEFAULT NULL,
  `num` int(11) NOT NULL DEFAULT '1',
  `createTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `clientId` int(11) NOT NULL,
  `vender` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  PRIMARY KEY (`orderId`),
  KEY `orderIdIndex` (`orderId`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

#创建商品表
DROP TABLE IF EXISTS  `t_points_product`;
CREATE TABLE `t_points_product` (
  `productId` int(11) NOT NULL AUTO_INCREMENT,
  `vender` int(11) NOT NULL,
  `productName` varchar(255) NOT NULL,
  `productImg` varchar(255) NOT NULL,
  `num` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `exchange` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`productId`),
  KEY `productIdIndex` (`productId`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4;

#创建兑换商品地址表
DROP TABLE IF EXISTS  `t_points_product_url`;
CREATE TABLE `t_points_product_url` (
  `urlId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `qrcode` varchar(255) NOT NULL,
  PRIMARY KEY (`urlId`),
  KEY `urlIdIndex` (`urlId`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4;

#创建二维码名片表
DROP TABLE IF EXISTS  `t_qrcode`;
CREATE TABLE `t_qrcode` (
  `qrcodeId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `phone` varchar(32) NOT NULL,
  `workPhone` varchar(32) NOT NULL,
  `email` varchar(100) NOT NULL,
  `company` varchar(128) NOT NULL,
  `company_url` varchar(255) NOT NULL,
  `company_address` varchar(255) NOT NULL,
  `qr` text NOT NULL,
  `logo` text NOT NULL,
  `qrX` double NOT NULL,
  `qrY` double NOT NULL,
  PRIMARY KEY (`qrcodeId`),
  KEY `qrcodeUserId` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=10060 DEFAULT CHARSET=utf8mb4;

#创建微信红包
DROP TABLE IF EXISTS  `t_red_pack`;
CREATE TABLE `t_red_pack` (
  `redPackId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `nickName` varchar(128) NOT NULL,
  `minMoney` int(11) NOT NULL,
  `maxMoney` int(11) NOT NULL,
  `wishing` varchar(128) NOT NULL,
  `actName` varchar(128) NOT NULL,
  `remark` varchar(128) NOT NULL,
  `state` int(11) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `maxPackNum` int(11) NOT NULL,
  `redPackRuleImage` varchar(128) NOT NULL,
  `redPackNoneTip` varchar(128) NOT NULL,
  PRIMARY KEY (`redPackId`),
  KEY `userIdIndex` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=10018 DEFAULT CHARSET=utf8mb4;

#创建微信红包列表
DROP TABLE IF EXISTS  `t_red_pack_client`;
CREATE TABLE `t_red_pack_client` (
  `redPackClientId` int(11) NOT NULL AUTO_INCREMENT,
  `redPackId` int(11) NOT NULL,
  `clientId` int(11) NOT NULL,
  `money` int(11) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`redPackClientId`),
  KEY `clientIdIndex` (`clientId`)
) ENGINE=InnoDB AUTO_INCREMENT=10294 DEFAULT CHARSET=utf8mb4;

#创建积分低于某个阶段提醒表
DROP TABLE IF EXISTS  `t_remind`;
CREATE TABLE `t_remind` (
  `remindId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  PRIMARY KEY (`remindId`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4;

#创建积分明细表
DROP TABLE IF EXISTS  `t_score_log`;
CREATE TABLE `t_score_log` (
  `scoreId` int(11) NOT NULL AUTO_INCREMENT,
  `clientId` int(11) NOT NULL,
  `score` int(11) NOT NULL DEFAULT '0',
  `dis` tinyint(1) NOT NULL DEFAULT '1',
  `remark` varchar(125) NOT NULL,
  `enjoyUrl` varchar(255) DEFAULT NULL,
  `event` int(11) NOT NULL DEFAULT '1',
  `createTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`scoreId`),
  KEY `scoreIdIndex` (`scoreId`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4;

#创建商城轮播图表
DROP TABLE IF EXISTS  `t_shop_banner`;
CREATE TABLE `t_shop_banner` (
  `userShopBannerId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `icon` varchar(256) NOT NULL,
  `title` varchar(256) NOT NULL,
  `sort` int(11) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`userShopBannerId`),
  KEY `userIdIndex` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=10016 DEFAULT CHARSET=utf8mb4;

#创建用户商城商品表
DROP TABLE IF EXISTS  `t_shop_commodity`;
CREATE TABLE `t_shop_commodity` (
  `shopCommodityId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `isLink` int(11) NOT NULL,
  `shopLinkCommodityId` int(11) NOT NULL,
  `shopCommodityClassifyId` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `icon` varchar(128) NOT NULL,
  `introduction` varchar(128) NOT NULL,
  `detail` text NOT NULL,
  `price` int(11) NOT NULL,
  `oldPrice` int(11) NOT NULL,
  `inventory` int(11) NOT NULL,
  `state` int(11) NOT NULL,
  `remark` varchar(128) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`shopCommodityId`),
  KEY `userIdIndex` (`userId`),
  KEY `shopCommodityClassifyIdIndex` (`shopCommodityClassifyId`)
) ENGINE=InnoDB AUTO_INCREMENT=10274 DEFAULT CHARSET=utf8mb4;

#创建用户商城商品分类表
DROP TABLE IF EXISTS  `t_shop_commodity_classify`;
CREATE TABLE `t_shop_commodity_classify` (
  `shopCommodityClassifyId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `icon` varchar(128) NOT NULL,
  `parent` int(11) NOT NULL,
  `sort` int(11) NOT NULL,
  `remark` varchar(128) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `link` varchar(128) NOT NULL,
  PRIMARY KEY (`shopCommodityClassifyId`),
  KEY `userIdIndex` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=10069 DEFAULT CHARSET=utf8mb4;

#创建商城订单表
DROP TABLE IF EXISTS  `t_shop_order`;
CREATE TABLE `t_shop_order` (
  `shopOrderId` varchar(32) NOT NULL,
  `userId` int(11) NOT NULL,
  `clientId` int(11) NOT NULL,
  `entranceUserId` int(11) NOT NULL,
  `image` varchar(128) NOT NULL,
  `description` varchar(128) NOT NULL,
  `price` int(11) NOT NULL,
  `num` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `wxPrePayId` varchar(128) NOT NULL,
  `state` int(11) NOT NULL,
  `remark` varchar(128) NOT NULL,
  `expressageName` varchar(20) NOT NULL DEFAULT '0',
  `expressageNum` varchar(30) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`shopOrderId`),
  KEY `matchIndex` (`userId`,`clientId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

#创建用户订单地址表
DROP TABLE IF EXISTS  `t_shop_order_address`;
CREATE TABLE `t_shop_order_address` (
  `shopOrderAddressId` int(11) NOT NULL AUTO_INCREMENT,
  `shopOrderId` varchar(32) NOT NULL,
  `name` varchar(32) NOT NULL,
  `province` varchar(32) NOT NULL,
  `city` varchar(32) NOT NULL,
  `address` varchar(128) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `payment` int(11) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`shopOrderAddressId`),
  KEY `shopOrderIdIndex` (`shopOrderId`)
) ENGINE=InnoDB AUTO_INCREMENT=10129 DEFAULT CHARSET=utf8mb4;

#创建用户订单商品表
DROP TABLE IF EXISTS  `t_shop_order_commodity`;
CREATE TABLE `t_shop_order_commodity` (
  `shopOrderCommodityId` int(11) NOT NULL AUTO_INCREMENT,
  `shopOrderId` varchar(32) NOT NULL,
  `shopCommodityId` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `icon` varchar(128) NOT NULL,
  `introduction` varchar(128) NOT NULL,
  `price` int(11) NOT NULL,
  `oldPrice` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`shopOrderCommodityId`),
  KEY `shopOrderIdIndex` (`shopOrderId`)
) ENGINE=InnoDB AUTO_INCREMENT=10139 DEFAULT CHARSET=utf8mb4;

#创建用户购物车表
DROP TABLE IF EXISTS  `t_shop_troller`;
CREATE TABLE `t_shop_troller` (
  `shopTrollerId` int(11) NOT NULL AUTO_INCREMENT,
  `clientId` int(11) NOT NULL,
  `shopCommodityId` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `icon` varchar(128) NOT NULL,
  `introduction` varchar(128) NOT NULL,
  `price` int(11) NOT NULL,
  `oldPrice` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`shopTrollerId`),
  KEY `clientIdIndex` (`clientId`)
) ENGINE=InnoDB AUTO_INCREMENT=10226 DEFAULT CHARSET=utf8mb4;

#创建用户表
DROP TABLE IF EXISTS  `t_user`;
CREATE TABLE `t_user` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `password` char(60) NOT NULL,
  `company` varchar(128) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `type` int(11) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `downDistributionNum` int(11) NOT NULL,
  `telephone` varchar(11) NOT NULL,
  `followLink` varchar(256) DEFAULT NULL,
  `sex` tinyint(1) NOT NULL DEFAULT '1',
  `birthday` date DEFAULT NULL,
  `openId` varchar(255) DEFAULT NULL,
  `openIdInfo` text,
  `distributionNum` int(11) NOT NULL DEFAULT '1',
  `clientId` int(11) DEFAULT NULL,
  `qrcode` varchar(255) DEFAULT NULL,
  `qrcodeCreateTime` int(11) DEFAULT NULL,
  `ticket` varchar(255) DEFAULT NULL,
  `score` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userId`),
  KEY `nameIndex` (`name`,`password`)
) ENGINE=InnoDB AUTO_INCREMENT=10109 DEFAULT CHARSET=utf8mb4;

#创建用户AppId与AppKey入口
DROP TABLE IF EXISTS  `t_user_app`;
CREATE TABLE `t_user_app` (
  `userAppId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `appLogo` varchar(128) NOT NULL,
  `poster` varchar(128) DEFAULT NULL,
  `appBg` varchar(128) NOT NULL,
  `appName` varchar(128) NOT NULL,
  `weixinNum` varchar(128) DEFAULT NULL,
  `appId` varchar(128) NOT NULL,
  `appKey` varchar(128) NOT NULL,
  `mchId` varchar(128) NOT NULL,
  `mchKey` varchar(128) NOT NULL,
  `mchSslCert` varchar(128) NOT NULL,
  `mchSslKey` varchar(128) NOT NULL,
  `remark` varchar(128) NOT NULL,
  `customService` varchar(255) DEFAULT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `appAccessToken` varchar(128) NOT NULL,
  `appAccessTokenExpire` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `appJsApiTicket` varchar(128) NOT NULL,
  `appJsApiTicketExpire` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cardTicket` varchar(128) NOT NULL,
  `cardTicketExpire` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`userAppId`),
  KEY `userIdIndex` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=10052 DEFAULT CHARSET=utf8mb4;

#创建代理商的客户表
DROP TABLE IF EXISTS  `t_user_client`;
CREATE TABLE `t_user_client` (
  `userClientId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `clientUserId` int(11) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`userClientId`),
  KEY `userIdIndex` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4;

#创建用户公司文章表
DROP TABLE IF EXISTS  `t_user_company_article`;
CREATE TABLE `t_user_company_article` (
  `userCompanyArticleId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `cover` varchar(128) NOT NULL,
  `title` varchar(128) NOT NULL,
  `remark` varchar(256) NOT NULL,
  `summary` varchar(256) NOT NULL,
  `content` text NOT NULL,
  `userCompanyClassifyId` int(11) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`userCompanyArticleId`),
  KEY `userIdIndex` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=10116 DEFAULT CHARSET=utf8mb4;

#创建用户公司广告表
DROP TABLE IF EXISTS  `t_user_company_banner`;
CREATE TABLE `t_user_company_banner` (
  `userCompanyBannerId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `title` varchar(256) NOT NULL,
  `image` varchar(256) NOT NULL,
  `url` varchar(256) NOT NULL,
  `sort` int(11) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`userCompanyBannerId`),
  KEY `userIdIndex` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=10040 DEFAULT CHARSET=utf8mb4;

#创建用户公司文章分类表
DROP TABLE IF EXISTS  `t_user_company_classify`;
CREATE TABLE `t_user_company_classify` (
  `userCompanyClassifyId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `icon` varchar(128) NOT NULL,
  `sort` int(11) NOT NULL,
  `remark` varchar(128) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `link` varchar(128) NOT NULL,
  PRIMARY KEY (`userCompanyClassifyId`),
  KEY `userIdIndex` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=10107 DEFAULT CHARSET=utf8mb4;

#公司联系我们
DROP TABLE IF EXISTS  `t_user_company_contact`;
CREATE TABLE `t_user_company_contact` (
  `contactId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `latitude` varchar(30) DEFAULT NULL,
  `longitude` varchar(30) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  `address` varchar(128) DEFAULT NULL,
  `scale` int(2) DEFAULT NULL,
  `infoUrl` varchar(128) DEFAULT NULL,
  `content` text,
  PRIMARY KEY (`contactId`),
  KEY `contactIndex` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=10006 DEFAULT CHARSET=utf8mb4;

#创建用户公司模板表
DROP TABLE IF EXISTS  `t_user_company_template`;
CREATE TABLE `t_user_company_template` (
  `userCompanyTemplateId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `companyTemplateId` int(11) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`userCompanyTemplateId`),
  KEY `userIdIndex` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=10217 DEFAULT CHARSET=utf8mb4;

#创建用户权限表
DROP TABLE IF EXISTS  `t_user_permission`;
CREATE TABLE `t_user_permission` (
  `userPermissionId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `permissionId` int(11) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`userPermissionId`),
  KEY `userIdIndex` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=10419 DEFAULT CHARSET=utf8mb4;

#创建VIP设置
DROP TABLE IF EXISTS  `t_vip`;
CREATE TABLE `t_vip` (
  `vipId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `cardImage` varchar(128) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`vipId`),
  KEY `userIdIndex` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=10016 DEFAULT CHARSET=utf8mb4;

#创建VIP用户设置
DROP TABLE IF EXISTS  `t_vip_client`;
CREATE TABLE `t_vip_client` (
  `vipClientId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `clientId` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `score` int(11) NOT NULL,
  `userCardCode` varchar(125) NOT NULL,
  `card_id` varchar(125) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`vipClientId`),
  KEY `userIdIndex` (`userId`),
  KEY `clientIdIndex` (`clientId`)
) ENGINE=InnoDB AUTO_INCREMENT=10024 DEFAULT CHARSET=utf8mb4;

#微信多客服列表
DROP TABLE IF EXISTS  `t_weixin_kf`;
CREATE TABLE `t_weixin_kf` (
  `kfId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `kf_id` int(11) DEFAULT NULL,
  `kf_nick` varchar(50) DEFAULT NULL,
  `kf_account` varchar(50) DEFAULT NULL,
  `kf_headimgurl` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`kfId`),
  KEY `weixinKfIndex` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=10019 DEFAULT CHARSET=utf8mb4;

#微信自动回复素材列表
DROP TABLE IF EXISTS  `t_weixin_material`;
CREATE TABLE `t_weixin_material` (
  `materialId` int(11) NOT NULL AUTO_INCREMENT,
  `weixinSubscribeId` int(11) NOT NULL,
  `Title` varchar(128) DEFAULT NULL,
  `Description` varchar(128) DEFAULT NULL,
  `Url` varchar(256) DEFAULT NULL,
  `PicUrl` varchar(128) DEFAULT NULL,
  `sort` int(11) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`materialId`),
  KEY `weixinSubscribeIdIndex` (`weixinSubscribeId`)
) ENGINE=InnoDB AUTO_INCREMENT=10031 DEFAULT CHARSET=utf8mb4;

#微信自定义菜单
DROP TABLE IF EXISTS  `t_weixin_menu`;
CREATE TABLE `t_weixin_menu` (
  `menuId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `content` text,
  PRIMARY KEY (`menuId`),
  KEY `weixinmenuIndex` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=10012 DEFAULT CHARSET=utf8mb4;

#微信被关注回复列表
DROP TABLE IF EXISTS  `t_weixin_subscribe`;
CREATE TABLE `t_weixin_subscribe` (
  `weixinSubscribeId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `materialClassifyId` int(11) NOT NULL,
  `title` varchar(128) DEFAULT NULL,
  `remark` varchar(128) DEFAULT NULL,
  `isRelease` int(11) DEFAULT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifyTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`weixinSubscribeId`),
  KEY `userIdIndex` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=10007 DEFAULT CHARSET=utf8mb4;

#创建模板信息列表
DROP TABLE IF EXISTS  `t_weixin_template`;
CREATE TABLE `t_weixin_template` (
  `userId` int(11) NOT NULL,
  `openState` int(1) NOT NULL DEFAULT '0',
  `TM00015` varchar(80) DEFAULT NULL,
  `TM00505` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`userId`),
  KEY `userIdIndex` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

#提现申请表
DROP TABLE IF EXISTS  `t_withdraw`;
CREATE TABLE `t_withdraw` (
  `withDrawId` int(11) NOT NULL AUTO_INCREMENT,
  `vender` int(11) NOT NULL,
  `clientId` int(11) NOT NULL,
  `money` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `bank` varchar(32) NOT NULL,
  `cardNo` varchar(128) NOT NULL,
  `mobile` varchar(32) NOT NULL,
  `remark` varchar(32) DEFAULT NULL,
  `state` int(1) NOT NULL DEFAULT '0',
  `createTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifyTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`withDrawId`),
  KEY `withDrawIdIndex` (`withDrawId`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

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
(10003,'oMhf-txr18KIBU1GZ0TXpxToaoH8',2),
(10004,'微信测试用户虚拟OpenId',2),
(10005,'微信测试用户虚拟OpenId',2),
(10006,'微信测试用户虚拟OpenId',2);

insert into t_address(clientId,name,province,city,address,phone,payment)values
(10001,'黎锦伟','广东','佛山','某地','15018749403',1);

insert into t_user_app(userId,appName,appId,appKey,mchId,mchKey,mchSslCert,mchSslKey,remark)values
(10003,'至高商城','wx5cc2d94dfe468c95','adc38d0974b0617023012fef684e9ae6','1220218001','56344f19b3b90eb545bf2f07800e7a10','/data/upload/apiclient_cert.pem','/data/upload/apiclient_key.pem',''),
(10004,'至强商城','testAppId','testAppKey','testMchId','testMchKey','testMchSslCert','testMchSslKey','');

insert into t_company_template(title,url,remark,type)values
('metro风格','/data/upload/template/sample1','',1),
('简约风格(测试用)','/data/upload/template/sample2','',1);

insert into t_user_company_template(userId,companyTemplateId,type)values
(10003,10001,1),
(10004,10001,1);

insert into t_user_company_classify(userId,title,icon,sort,remark)values
(10003,'行业新闻','/data/upload/sample/earth.png',1,''),
(10003,'公司文化','/data/upload/sample/cup.png',2,''),
(10003,'公司介绍','/data/upload/sample/home.png',3,''),
(10003,'数码产品','/data/upload/sample/picture.png',4,'');

insert into t_user_company_article(userId,cover,title,summary,remark,content,userCompanyClassifyId,sort)values
(10003,'/data/upload/sample.jpg','文章标题1','文章简介1','','文章内容1',10001,1),
(10003,'/data/upload/sample.jpg','文章标题2','文章简介2','','文章内容2',10002,2),
(10003,'/data/upload/sample.jpg','文章标题3','文章简介3','','文章内容3',10003,3),
(10003,'/data/upload/sample.jpg','文章标题4','文章简介4','','文章内容4',10004,4),
(10003,'/data/upload/sample.jpg','文章标题5','文章简介5','','文章内容5',10001,5);

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

insert into t_shop_commodity(userId,shopLinkCommodityId, isLink, shopCommodityClassifyId,icon,title,introduction,detail,price,oldPrice,inventory,state,sort)values
(10003,0, 0, 10002,'/data/upload/sample/sample4.jpg','商品1','商品简介1','商品描述1',1,11100,10,1,1),
(10003,0, 0, 10003,'/data/upload/sample/sample5.jpg','商品2','商品简介2','商品描述2',2,22200,10,1,2),
(10003,0, 0, 10004,'/data/upload/sample/sample6.jpg','商品3','商品简介3','商品描述3',3,33300,10,1,3),
(10003,10005, 1, 10002,'','','','',1,1,1,1,4),
(10004,0, 0, 10009,'/data/upload/sample/sample6.jpg','商品4','商品简介4','商品描述4',4,44400,10,1,5),
(10004,10001, 1, 10009,'/data/upload/sample/sample6.jpg','商品4','商品简介4','商品描述4',4,44400,10,1,6);

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

insert into t_lucky_draw_commodity(luckyDrawId,title,image,type,quantity,sort)values
(10001,'iphone1','/data/upload/sample/sample1.png',1,10,1),
(10001,'iphone2','/data/upload/sample/sample2.png',1,10,2),
(10001,'iphone3','/data/upload/sample/sample3.png',1,10,3),
(10001,'iphone4','/data/upload/sample/sample4.png',1,10,4),
(10001,'iphone5','/data/upload/sample/sample5.png',1,10,5),
(10001,'iphone6','/data/upload/sample/sample6.png',1,10,6),
(10001,'iphone7','/data/upload/sample/sample7.png',1,10,7),
(10001,'iphone8','/data/upload/sample/sample8.png',2,10,8);

insert into t_vip(userId,cardImage)values
(10003,'/data/upload/sample/vip.jpg');

insert into t_red_pack(userId,nickName,minMoney,maxMoney,wishing,actName,remark,state,maxPackNum,redPackRuleImage,redPackNoneTip)values
(10003,'至高商城',100,100,'红包','红包','红包',1,10,'/data/upload/sample/redpackrule.jpg','迟来一步，红包都被禽兽抢光了');

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