create table #prefix#products (
	id integer primary key,
	name char(72) not null,
	price int not null,
	photo char(255) not null default '',
	category char(72) not null default '',
	description text not null default '',
	download char(255) not null default '',
	quantity int not null default 0,
	taxes char(255) not null default '[]',
	address tinyint not null default 0
);

create index #prefix#products_name on #prefix#products (name);
create index #prefix#products_by_category on #prefix#products (category, name);

create table #prefix#products_category (
	name char(72) not null
);

create unique index #prefix#products_category_name on #prefix#products_category (name);

create table #prefix#products_taxes (
	name char(24) not null,
	percent int not null
);

create unique index #prefix#products_tax on #prefix#products_taxes (name);
