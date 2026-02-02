CREATE TABLE IF NOT EXISTS #__jsports_bulletin_types (
	 bulletintype varchar(1) NOT NULL,
	 typedesc varchar(30) not null,
	 publicaccess tinyint default 0, 
 PRIMARY KEY (`bulletintype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

insert into #__jsports_bulletin_types values('G','General',0);
insert into #__jsports_bulletin_types values('T','Tournament',0);
insert into #__jsports_bulletin_types values('Y','Tryout',1);
insert into #__jsports_bulletin_types values('F','Fundraising',0);

