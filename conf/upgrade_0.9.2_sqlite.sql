alter table #prefix#products add column shipping int not null default 0;
alter table #prefix#products_taxes add column charge_on_shipping int not null default 0;

create table #prefix#products_order (
	id integer primary key,
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
