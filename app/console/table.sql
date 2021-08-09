create table `user` (
    `id` int unsigned not null auto_increment primary key ,
    `name` varchar(128) not null unique ,
    `password` varchar(32) not null default '',
    `role` tinyint unsigned not null default 0,
    `created` datetime,
    `updated` datetime
)ENGINE=InnoDB,charset=utf8mb4;

create table `msg` (
    `id` int unsigned not null auto_increment primary key ,
    `uid` int unsigned not null,
    `to_uid` int unsigned not null default 0,
    `group_id` int  unsigned not null default 0,
    `content` text,
    `created` datetime,
    `updated` datetime
)ENGINE=InnoDB,charset=utf8mb4;

alter table `msg` add index uid_idx (uid)