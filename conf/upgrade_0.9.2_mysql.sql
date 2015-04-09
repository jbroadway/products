alter table #prefix#products add column shipping int not null default 0;
alter table #prefix#products_taxes add column charge_on_shipping int not null default 0;
