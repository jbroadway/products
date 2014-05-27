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
	index (name)
	index (category, name)
);

create table #prefix#products_category (
	name char(72) not null primary key
);

create table #prefix#products_taxes (
	name char(24) not null primary key,
	percent int not null
);
