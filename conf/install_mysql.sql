create table #prefix#products (
	id int not null auto_increment primary key, 
	name char(72) not null, 
	price int not null, 
	photo char(255) not null default '', 
	category char(72) not null default '', 
	description text not null default '', 
	download char(255) not null default '', 
	quantity int not null default 0, 
	taxes char(255) not null default '[]',
	address tinyint not null default 0,
	details text,
	shipping int not null default 0,
	index (name),
	index (category, name)
);

create table #prefix#products_category (
	name char(72) not null primary key
);

create table #prefix#products_taxes (
	name char(24) not null primary key,
	percent int not null,
	charge_on_shipping int not null default 0
);

create table #prefix#products_order (
	id int not null auto_increment primary key,
	user_id int not null,
	payment_id int not null,
	ts datetime not null,
	status char(32) not null default 'pending',
	subtotal int not null,
	shipping int not null,
	taxes text,
	total int not null,
	items text,
	index (user_id, status, ts)
);
