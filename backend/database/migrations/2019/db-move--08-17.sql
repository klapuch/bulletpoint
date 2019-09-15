INSERT INTO deploy.migrations(filename) VALUES('database/migrations/2019/db-move--08-17.sql');

UPDATE deploy.migrations SET filename = 'database/' || filename WHERE left(filename, length('database')) != 'database';
