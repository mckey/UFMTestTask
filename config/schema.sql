create database if not exists `ifm_test` character set utf8 collate utf8_general_ci;

use mysql;

grant all privileges on *.* to 'root'@'%' with grant option;
grant all privileges on `ifm_test`.* to 'ifm'@'%';

flush privileges;

use ifm_test;

drop table if exists banner_view_stat;

create table banner_view_stat
(
    ip_address  varchar(15)             not null,
    user_agent  varchar(255) default '' not null,
    view_date   datetime                not null,
    page_url    varchar(255) default '' not null,
    views_count bigint       default 1  not null,

    primary key (ip_address, user_agent, page_url)
) charset = utf8;
