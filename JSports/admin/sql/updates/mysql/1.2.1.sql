CREATE TABLE IF NOT EXISTS #__jsports_bulletin_categories (
	 category varchar(1) NOT NULL,
	 catdesc varchar(30) not null,
	 publicaccess tinyint default 0, 
 PRIMARY KEY (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

insert into #__jsports_bulletin_categories values('G','General',0);
insert into #__jsports_bulletin_categories values('T','Tournament',0);
insert into #__jsports_bulletin_categories values('Y','Tryout',1);
insert into #__jsports_bulletin_categories values('F','Fundraising',0);
insert into #__jsports_bulletin_categories values('S','Sponsors',0);

