#创建数据库
drop database if exists DistributerManager;
create database DistributerManager;
use DistributerManager;

#创建用户表
create table t_user(
	userId integer not null auto_increment,
	name char(32) not null,
	password char(48) not null,
	type integer not null,
	createTime timestamp not null default CURRENT_TIMESTAMP,
	modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, 
	primary key( userId )
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

alter table t_user add index nameIndex(name,password);

#创建session表
create table ci_sessions (
  session_id char(40) default '0' not null,
  ip_address char(45) default '0' not null,
  user_agent char(120) not null,
  last_activity int(10) unsigned default 0 not null,
  user_data text default '' not null,
  primary key (session_id),
  key `last_activity_idx` (`last_activity`)
);

#建立初始数据
insert into t_user(userId,name,password,type) values
(10001,"fish",SHA1("123456"),1);

#显示初始数据
select * from t_user;
