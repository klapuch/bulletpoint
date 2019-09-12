INSERT INTO filesystem.files$images (filename, size_bytes, mime_type, width, height) VALUES ('images/avatars/0.png', 100, 'image/png', 180, 180);

INSERT INTO public.users (username, email, password, role) VALUES ('klapuch', 'klapuchdominik@gmail.com', '$argon2i$v=19$m=131072,t=4,p=4$ZEhjRjVaYzYuYkg2VUQwcQ$EuRHYiI+7NJoVtkcdbPTh4QUHxWw7FOncPphl18ZGq4', 'admin');
INSERT INTO public.users (username, email, password, role) VALUES ('test', 'test@gmail.com', '$argon2i$v=19$m=131072,t=4,p=4$ZEhjRjVaYzYuYkg2VUQwcQ$EuRHYiI+7NJoVtkcdbPTh4QUHxWw7FOncPphl18ZGq4', 'member');

UPDATE access.verification_codes SET used_at = now() WHERE user_id = 1;
UPDATE access.verification_codes SET used_at = now() WHERE user_id = 2;

INSERT INTO public."references" (url) VALUES ('https://wikipedia.org/wiki/PHP');
INSERT INTO public."references" (url) VALUES ('https://cs.wikipedia.org/wiki/Objektov%C4%9B_orientovan%C3%A9_programov%C3%A1n%C3%AD');
INSERT INTO public."references" (url) VALUES ('https://www.postgresql.org/');
INSERT INTO public."references" (url) VALUES ('https://cs.wikipedia.org/wiki/Otev%C5%99en%C3%BD_software');
INSERT INTO public."references" (url) VALUES ('https://www.json.org/');
INSERT INTO public."references" (url) VALUES ('https://git-scm.com/');
INSERT INTO public."references" (url) VALUES ('https://cs.wikipedia.org/wiki/Kongruence_(psychologie)');
INSERT INTO public."references" (url) VALUES ('https://www.coffeespot.cz/rozdily-kava-arabica-robusta');
INSERT INTO public."references" (url) VALUES ('https://cs.wikipedia.org/wiki/Kofein');
INSERT INTO public."references" (url) VALUES ('https://www.coffeespot.cz/rozdily-kava-arabica-robusta');

INSERT INTO public.themes (name, reference_id, user_id, created_at) VALUES ('PHP', 1, 1, '2019-01-13 19:02:27.372965+01');
INSERT INTO public.themes (name, reference_id, user_id, created_at) VALUES ('OOP', 2, 1, '2019-01-13 19:02:27.377934+01');
INSERT INTO public.themes (name, reference_id, user_id, created_at) VALUES ('PostgreSQL', 3, 1, '2019-01-19 11:56:43.545366+01');
INSERT INTO public.themes (name, reference_id, user_id, created_at) VALUES ('Open Source', 4, 1, '2019-01-19 11:58:24.138128+01');
INSERT INTO public.themes (name, reference_id, user_id, created_at) VALUES ('JSON', 5, 1, '2019-01-19 12:45:15.436224+01');
INSERT INTO public.themes (name, reference_id, user_id, created_at) VALUES ('Git', 6, 1, '2019-01-20 19:24:49.99384+01');
INSERT INTO public.themes (name, reference_id, user_id, created_at) VALUES ('Kongruence', 7, 1, '2019-02-10 23:28:18.560324+01');
INSERT INTO public.themes (name, reference_id, user_id, created_at) VALUES ('Arabica', 8, 1, now());
INSERT INTO public.themes (name, reference_id, user_id, created_at) VALUES ('Kofein', 9, 1, now());
INSERT INTO public.themes (name, reference_id, user_id, created_at) VALUES ('Robusta', 10, 1, now());

INSERT INTO public.public_bulletpoints (theme_id, source_id, user_id, content, created_at) VALUES (1, 1, 1, 'Strmá křivka učení', '2019-01-13 19:02:27.392957+01');
INSERT INTO public.public_bulletpoints (theme_id, source_id, user_id, content, created_at) VALUES (1, 2, 1, 'Datové typy', '2019-01-13 19:02:27.402929+01');
INSERT INTO public.public_bulletpoints (theme_id, source_id, user_id, content, created_at) VALUES (1, 5, 1, '[[Objektově orientovaný]]', '2019-01-13 19:02:27.406272+01');
INSERT INTO public.public_bulletpoints (theme_id, source_id, user_id, content, created_at) VALUES (2, 4, 1, 'Zapouzdření', '2019-01-13 19:02:27.409961+01');
INSERT INTO public.public_bulletpoints (theme_id, source_id, user_id, content, created_at) VALUES (3, 6, 1, '[[Open Source]]', '2019-01-19 11:57:34.252092+01');
INSERT INTO public.public_bulletpoints (theme_id, source_id, user_id, content, created_at) VALUES (4, 7, 1, 'Otevřený zdrojový kód', '2019-01-19 11:58:55.472299+01');
INSERT INTO public.public_bulletpoints (theme_id, source_id, user_id, content, created_at) VALUES (5, 8, 1, 'Datový formát', '2019-01-19 12:45:50.675016+01');
INSERT INTO public.public_bulletpoints (theme_id, source_id, user_id, content, created_at) VALUES (3, 9, 1, '[[JSON]] podpora', '2019-01-19 12:46:37.97745+01');
INSERT INTO public.public_bulletpoints (theme_id, source_id, user_id, content, created_at) VALUES (6, 10, 1, '[[Open source]]', '2019-01-20 19:25:28.708165+01');
INSERT INTO public.public_bulletpoints (theme_id, source_id, user_id, content, created_at) VALUES (6, 11, 1, 'Autor Linus Torvalds', '2019-01-20 19:25:52.422695+01');
INSERT INTO public.public_bulletpoints (theme_id, source_id, user_id, content, created_at) VALUES (6, 12, 1, 'Vydáno 2005', '2019-01-20 19:26:14.824176+01');
INSERT INTO public.public_bulletpoints (theme_id, source_id, user_id, content, created_at) VALUES (6, 13, 1, 'Distribuovaný vývoj', '2019-01-20 19:27:31.693743+01');
INSERT INTO public.public_bulletpoints (theme_id, source_id, user_id, content, created_at) VALUES (7, 14, 1, 'Soulad mezi verbálním a neverbálním chování', '2019-02-10 23:30:04.547761+01');
INSERT INTO public.public_bulletpoints (theme_id, source_id, user_id, content, created_at) VALUES (8, 15, 1, '2x více cukru', now());
INSERT INTO public.public_bulletpoints (theme_id, source_id, user_id, content, created_at) VALUES (8, 16, 1, 'Obsahuje 2-3× méně [[kofeinu]]', now());
INSERT INTO public.public_bulletpoints (theme_id, source_id, user_id, content, created_at) VALUES (2, 17, 1, 'Java', now());
INSERT INTO public.public_bulletpoints (theme_id, source_id, user_id, content, created_at) VALUES (2, 18, 1, 'C#', now());
INSERT INTO public.public_bulletpoints (theme_id, source_id, user_id, content, created_at) VALUES (2, 19, 1, '[[PHP]]', now());

INSERT INTO public.sources (link, type) VALUES (NULL, 'head');
INSERT INTO public.sources (link, type) VALUES ('https://wikipedia.org/wiki/PHP', 'web');
INSERT INTO public.sources (link, type) VALUES ('https://wikipedia.org/wiki/PHP', 'web');
INSERT INTO public.sources (link, type) VALUES ('https://en.wikipedia.org/wiki/Encapsulation_(computer_programming)', 'web');
INSERT INTO public.sources (link, type) VALUES ('https://wikipedia.org/wiki/PHP', 'web');
INSERT INTO public.sources (link, type) VALUES ('https://cs.wikipedia.org/wiki/PostgreSQL', 'web');
INSERT INTO public.sources (link, type) VALUES ('https://cs.wikipedia.org/wiki/Otev%C5%99en%C3%BD_software', 'web');
INSERT INTO public.sources (link, type) VALUES ('https://cs.wikipedia.org/wiki/JavaScript_Object_Notation', 'web');
INSERT INTO public.sources (link, type) VALUES ('https://www.postgresql.org/docs/current/functions-json.html', 'web');
INSERT INTO public.sources (link, type) VALUES ('https://cs.m.wikipedia.org/wiki/Git', 'web');
INSERT INTO public.sources (link, type) VALUES ('https://cs.m.wikipedia.org/wiki/Git', 'web');
INSERT INTO public.sources (link, type) VALUES ('https://cs.m.wikipedia.org/wiki/Git', 'web');
INSERT INTO public.sources (link, type) VALUES ('https://cs.m.wikipedia.org/wiki/Git', 'web');
INSERT INTO public.sources (link, type) VALUES ('http://www.psychologie-ovlivnovani.cz/', 'web');
INSERT INTO public.sources (link, type) VALUES ('https://www.coffeespot.cz/rozdily-kava-arabica-robusta', 'web');
INSERT INTO public.sources (link, type) VALUES ('https://www.coffeespot.cz/rozdily-kava-arabica-robusta', 'web');
INSERT INTO public.sources (link, type) VALUES ('https://en.wikipedia.org/wiki/Java_(programming_language)', 'web');
INSERT INTO public.sources (link, type) VALUES ('https://en.wikipedia.org/wiki/C_Sharp_(programming_language)', 'web');
INSERT INTO public.sources (link, type) VALUES ('https://en.wikipedia.org/wiki/PHP', 'web');

INSERT INTO bulletpoint_referenced_themes(bulletpoint_id, theme_id) VALUES (3, 2);
INSERT INTO bulletpoint_referenced_themes(bulletpoint_id, theme_id) VALUES (5, 4);
INSERT INTO bulletpoint_referenced_themes(bulletpoint_id, theme_id) VALUES (8, 5);
INSERT INTO bulletpoint_referenced_themes(bulletpoint_id, theme_id) VALUES (9, 4);
INSERT INTO bulletpoint_referenced_themes(bulletpoint_id, theme_id) VALUES (15, 9);
INSERT INTO bulletpoint_referenced_themes(bulletpoint_id, theme_id) VALUES (18, 1);

INSERT INTO public.tags (name) VALUES ('programovací jazyk');
INSERT INTO public.tags (name) VALUES ('IT');
INSERT INTO public.tags (name) VALUES ('Software');
INSERT INTO public.tags (name) VALUES ('Databáze');
INSERT INTO public.tags (name) VALUES ('Psychologie');
INSERT INTO public.tags (name) VALUES ('Káva');

INSERT INTO public.theme_alternative_names (name, theme_id) VALUES ('Objektově orientované programování', 2);
INSERT INTO public.theme_alternative_names (name, theme_id) VALUES ('PG', 3);
INSERT INTO public.theme_alternative_names (name, theme_id) VALUES ('postgres', 3);
INSERT INTO public.theme_alternative_names (name, theme_id) VALUES ('OSS', 4);

INSERT INTO public.theme_tags (theme_id, tag_id) VALUES (2, 1);
INSERT INTO public.theme_tags (theme_id, tag_id) VALUES (1, 1);
INSERT INTO public.theme_tags (theme_id, tag_id) VALUES (3, 4);
INSERT INTO public.theme_tags (theme_id, tag_id) VALUES (4, 3);
INSERT INTO public.theme_tags (theme_id, tag_id) VALUES (5, 3);
INSERT INTO public.theme_tags (theme_id, tag_id) VALUES (6, 3);
INSERT INTO public.theme_tags (theme_id, tag_id) VALUES (7, 5);
INSERT INTO public.theme_tags (theme_id, tag_id) VALUES (8, 6);
INSERT INTO public.theme_tags (theme_id, tag_id) VALUES (9, 6);
INSERT INTO public.theme_tags (theme_id, tag_id) VALUES (10, 6);

INSERT INTO contributed_bulletpoints (theme_id, source_id, user_id, content) VALUES (1, 3, 2, 'Test');

INSERT INTO bulletpoint_theme_comparisons (theme_id, bulletpoint_id) VALUES (10, 14);
INSERT INTO bulletpoint_theme_comparisons (theme_id, bulletpoint_id) VALUES (10, 15);

INSERT INTO bulletpoint_groups (bulletpoint_id, root_bulletpoint_id) VALUES (16, 17);
INSERT INTO bulletpoint_groups (bulletpoint_id, root_bulletpoint_id) VALUES (18, 17);

REFRESH MATERIALIZED VIEW CONCURRENTLY bulletpoint_reputations;
REFRESH MATERIALIZED VIEW CONCURRENTLY starred_themes;

INSERT INTO deploy.migrations(filename) VALUES('database/migrations/2019/78-testing_migration--02-24.sql');
INSERT INTO deploy.migrations(filename) VALUES('database/migrations/2019/81-tag_support--02-24.sql');
INSERT INTO deploy.migrations(filename) VALUES('database/migrations/2019/88-any_referenced_theme--03-02.sql');
INSERT INTO deploy.migrations(filename) VALUES('database/migrations/2019/master-groups_not_remove--05-11.sql');
INSERT INTO deploy.migrations(filename) VALUES('database/migrations/2019/db-move--08-17.sql');
INSERT INTO deploy.migrations(filename) VALUES('database/migrations/2019/json_to_array--09-01.sql');
INSERT INTO deploy.migrations(filename) VALUES('database/migrations/2019/mobile-references--08-18.sql');
INSERT INTO deploy.migrations(filename) VALUES('database/migrations/2019/range--06-23.sql');
INSERT INTO deploy.migrations(filename) VALUES('database/migrations/2019/user_tag_recount--06-23.sql');
INSERT INTO deploy.migrations(filename) VALUES('database/migrations/2019/bulletpoints_nulls_last--06-23.sql');
