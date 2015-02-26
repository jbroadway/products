alter table #prefix#products add column details text;
update #prefix#products set quantity = -1 where quantity = 0;
