INSERT INTO deploy.migrations(filename) VALUES('migrations/2019/migrations/2019/81--02-24--tag_support.sql');

ALTER TABLE public.tags ADD UNIQUE (name);
