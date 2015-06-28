create table #prefix#products (
	id serial not null primary key,
	name character(72) not null,
	price integer not null,
	photo character(255) not null default '',
	category character(72) not null default '',
	description text not null default '',
	download character(255) not null default '',
	quantity integer not null default 0,
	taxes character(255) not null default '[]',
	address tinyint not null default 0,
	details text,
	shipping integer not null default 0
);

create index #prefix#products_name on #prefix#products (name);
create index #prefix#products_by_category on #prefix#products (category, name);

create table #prefix#products_category (
	name character(72) not null
);

create unique index #prefix#products_category_name on #prefix#products_category (name);

create table #prefix#products_taxes (
	name character(24) not null,
	percent integer not null,
	charge_on_shipping int not null default 0
);

create unique index #prefix#products_tax on #prefix#products_taxes (name);

create table #prefix#products_order (
	id serial not null primary key,
	user_id int not null,
	payment_id int not null,
	ts datetime not null,
	status char(32) not null default 'pending',
	subtotal int not null,
	shipping int not null,
	taxes text,
	total int not null,
	items text
);

create index #prefix#products_order_user on #prefix#products_order (user_id, status, ts);
