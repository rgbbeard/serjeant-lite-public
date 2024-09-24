set names utf8;
set time_zone = '+00:00';
set foreign_key_checks = 0;
set sql_mode = 'no_auto_value_on_zero';

drop database if exists serjeant_lite;
create database serjeant_lite /*!40100 default character set utf8 */;

use serjeant_lite;


drop table if exists serjeant_administrators;
create table serjeant_administrators (
    id tinyint(1) not null auto_increment,
    username varchar(20) not null,
    password varchar(100) not null,
    display varchar(100) not null default username,
    disabled tinyint(1) not null default 0,
    primary key (id)
) engine=InnoDB default charset=utf8;


drop table if exists serjeant_users;
create table serjeant_users (
    id int(3) not null auto_increment,
    username varchar(100) not null,
    password varchar(100) not null,
    display varchar(100) not null,
    disabled tinyint(1) not null default 0,
    role tinyint(1) not null default 0,
    primary key (id)
) engine=InnoDB default charset=utf8;


drop table if exists serjeant_user_properties;
create table serjeant_user_properties (
    id int(10) not null auto_increment,
    user_id int(10) not null,
    name varchar(50) not null,
    value varchar(200) default null,
    primary key (id)
) engine=InnoDB default charset=utf8;


drop table if exists serjeant_user_pat;
create table serjeant_user_pat (
    id int(3) not null auto_increment,
    user_id int(3) not null,
    encrypted_token varchar(300) not null,
    disabled tinyint(1) not null default 0,
    date_created datetime not null default sysdate(),
    date_modified datetime not null default sysdate(),
    primary key (id)
) engine=InnoDB default charset=utf8;


drop table if exists serjeant_user_sessions;
create table serjeant_user_sessions (
    id int(5) not null auto_increment,
    user_id int(3) not null,
    value varchar(1000) not null,
    date_created datetime not null default sysdate(),
    primary key (id)
) engine=InnoDB default charset=utf8;


drop table if exists task_notes;
create table task_notes (
    id int(10) not null auto_increment,
    task_id int(10) not null,
    description mediumtext not null,
    date_created datetime not null default sysdate(),
    date_modified datetime not null default sysdate(),
    primary key (id)
) engine=InnoDB default charset=utf8;


drop table if exists task_properties;
create table task_properties (
    id int(10) not null auto_increment,
    task_id int(10) not null,
    name varchar(50) not null,
    value varchar(200) default null,
    primary key (id)
) engine=InnoDB default charset=utf8;


drop table if exists user_tasks;
create table user_tasks (
    id int(10) not null auto_increment,
    user_id int(3) not null,
    name varchar(300) not null,
    priority tinyint(1) not null,
    sort tinyint(2) not null default 1,
    date_created datetime not null default sysdate(),
    date_modified datetime not null default sysdate(),
    primary key (id)
) engine=InnoDB default charset=utf8;