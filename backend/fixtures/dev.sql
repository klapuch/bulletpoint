INSERT INTO public.users (id, username, email, password, role) OVERRIDING SYSTEM VALUE VALUES (1, 'klapuch', 'klapuchdominik@gmail.com', '$argon2i$v=19$m=131072,t=4,p=4$ZEhjRjVaYzYuYkg2VUQwcQ$EuRHYiI+7NJoVtkcdbPTh4QUHxWw7FOncPphl18ZGq4', 'admin');
INSERT INTO public.users (id, username, email, password, role) OVERRIDING SYSTEM VALUE VALUES (2, 'test', 'test@gmail.com', '$argon2i$v=19$m=131072,t=4,p=4$ZEhjRjVaYzYuYkg2VUQwcQ$EuRHYiI+7NJoVtkcdbPTh4QUHxWw7FOncPphl18ZGq4', 'member');

UPDATE access.verification_codes SET used_at = now() WHERE user_id = 1;
UPDATE access.verification_codes SET used_at = now() WHERE user_id = 2;

INSERT INTO public."references" (id, url) OVERRIDING SYSTEM VALUE VALUES (2, 'https://cs.wikipedia.org/wiki/Objektov%C4%9B_orientovan%C3%A9_programov%C3%A1n%C3%AD');
INSERT INTO public."references" (id, url) OVERRIDING SYSTEM VALUE VALUES (1, 'https://wikipedia.org/wiki/PHP');
INSERT INTO public."references" (id, url) OVERRIDING SYSTEM VALUE VALUES (3, 'https://www.postgresql.org/');
INSERT INTO public."references" (id, url) OVERRIDING SYSTEM VALUE VALUES (4, 'https://cs.wikipedia.org/wiki/Otev%C5%99en%C3%BD_software');
INSERT INTO public."references" (id, url) OVERRIDING SYSTEM VALUE VALUES (5, 'https://www.json.org/');
INSERT INTO public."references" (id, url) OVERRIDING SYSTEM VALUE VALUES (6, 'https://git-scm.com/');
INSERT INTO public."references" (id, url) OVERRIDING SYSTEM VALUE VALUES (7, 'https://cs.wikipedia.org/wiki/Kongruence_(psychologie)');

INSERT INTO public.themes (id, name, reference_id, user_id, created_at) OVERRIDING SYSTEM VALUE VALUES (2, 'OOP', 2, 1, '2019-01-13 19:02:27.377934+01');
INSERT INTO public.themes (id, name, reference_id, user_id, created_at) OVERRIDING SYSTEM VALUE VALUES (1, 'PHP', 1, 1, '2019-01-13 19:02:27.372965+01');
INSERT INTO public.themes (id, name, reference_id, user_id, created_at) OVERRIDING SYSTEM VALUE VALUES (3, 'PostgreSQL', 3, 1, '2019-01-19 11:56:43.545366+01');
INSERT INTO public.themes (id, name, reference_id, user_id, created_at) OVERRIDING SYSTEM VALUE VALUES (4, 'Open Source', 4, 1, '2019-01-19 11:58:24.138128+01');
INSERT INTO public.themes (id, name, reference_id, user_id, created_at) OVERRIDING SYSTEM VALUE VALUES (5, 'JSON', 5, 1, '2019-01-19 12:45:15.436224+01');
INSERT INTO public.themes (id, name, reference_id, user_id, created_at) OVERRIDING SYSTEM VALUE VALUES (6, 'Git', 6, 1, '2019-01-20 19:24:49.99384+01');
INSERT INTO public.themes (id, name, reference_id, user_id, created_at) OVERRIDING SYSTEM VALUE VALUES (7, 'Kongruence', 7, 1, '2019-02-10 23:28:18.560324+01');

INSERT INTO public.public_bulletpoints (id, theme_id, source_id, user_id, content, created_at) OVERRIDING SYSTEM VALUE VALUES (2, 1, 2, 1, 'Datové typy', '2019-01-13 19:02:27.402929+01');
INSERT INTO public.public_bulletpoints (id, theme_id, source_id, user_id, content, created_at) OVERRIDING SYSTEM VALUE VALUES (3, 1, 5, 1, 'Objektově orientovaný', '2019-01-13 19:02:27.406272+01');
INSERT INTO public.public_bulletpoints (id, theme_id, source_id, user_id, content, created_at) OVERRIDING SYSTEM VALUE VALUES (4, 2, 4, 1, 'Zapouzdření', '2019-01-13 19:02:27.409961+01');
INSERT INTO public.public_bulletpoints (id, theme_id, source_id, user_id, content, created_at) OVERRIDING SYSTEM VALUE VALUES (6, 4, 7, 1, 'Otevřený zdrojový kód', '2019-01-19 11:58:55.472299+01');
INSERT INTO public.public_bulletpoints (id, theme_id, source_id, user_id, content, created_at) OVERRIDING SYSTEM VALUE VALUES (5, 3, 6, 1, 'Open Source', '2019-01-19 11:57:34.252092+01');
INSERT INTO public.public_bulletpoints (id, theme_id, source_id, user_id, content, created_at) OVERRIDING SYSTEM VALUE VALUES (1, 1, 1, 1, 'Strmá křivka učení', '2019-01-13 19:02:27.392957+01');
INSERT INTO public.public_bulletpoints (id, theme_id, source_id, user_id, content, created_at) OVERRIDING SYSTEM VALUE VALUES (7, 5, 8, 1, 'Datový formát', '2019-01-19 12:45:50.675016+01');
INSERT INTO public.public_bulletpoints (id, theme_id, source_id, user_id, content, created_at) OVERRIDING SYSTEM VALUE VALUES (8, 3, 9, 1, 'JSON podpora', '2019-01-19 12:46:37.97745+01');
INSERT INTO public.public_bulletpoints (id, theme_id, source_id, user_id, content, created_at) OVERRIDING SYSTEM VALUE VALUES (9, 6, 10, 1, 'Open source', '2019-01-20 19:25:28.708165+01');
INSERT INTO public.public_bulletpoints (id, theme_id, source_id, user_id, content, created_at) OVERRIDING SYSTEM VALUE VALUES (10, 6, 11, 1, 'Autor Linus Torvalds', '2019-01-20 19:25:52.422695+01');
INSERT INTO public.public_bulletpoints (id, theme_id, source_id, user_id, content, created_at) OVERRIDING SYSTEM VALUE VALUES (11, 6, 12, 1, 'Vydáno 2005', '2019-01-20 19:26:14.824176+01');
INSERT INTO public.public_bulletpoints (id, theme_id, source_id, user_id, content, created_at) OVERRIDING SYSTEM VALUE VALUES (12, 6, 13, 1, 'Distribuovaný vývoj', '2019-01-20 19:27:31.693743+01');
INSERT INTO public.public_bulletpoints (id, theme_id, source_id, user_id, content, created_at) OVERRIDING SYSTEM VALUE VALUES (13, 7, 14, 1, 'Soulad mezi verbálním a neverbálním chování', '2019-02-10 23:30:04.547761+01');

INSERT INTO public.sources (id, link, type) OVERRIDING SYSTEM VALUE VALUES (2, 'https://wikipedia.org/wiki/PHP', 'web');
INSERT INTO public.sources (id, link, type) OVERRIDING SYSTEM VALUE VALUES (3, 'https://wikipedia.org/wiki/PHP', 'web');
INSERT INTO public.sources (id, link, type) OVERRIDING SYSTEM VALUE VALUES (4, 'https://en.wikipedia.org/wiki/Encapsulation_(computer_programming)', 'web');
INSERT INTO public.sources (id, link, type) OVERRIDING SYSTEM VALUE VALUES (5, 'https://wikipedia.org/wiki/PHP', 'web');
INSERT INTO public.sources (id, link, type) OVERRIDING SYSTEM VALUE VALUES (7, 'https://cs.wikipedia.org/wiki/Otev%C5%99en%C3%BD_software', 'web');
INSERT INTO public.sources (id, link, type) OVERRIDING SYSTEM VALUE VALUES (6, 'https://cs.wikipedia.org/wiki/PostgreSQL', 'web');
INSERT INTO public.sources (id, link, type) OVERRIDING SYSTEM VALUE VALUES (1, NULL, 'head');
INSERT INTO public.sources (id, link, type) OVERRIDING SYSTEM VALUE VALUES (8, 'https://cs.wikipedia.org/wiki/JavaScript_Object_Notation', 'web');
INSERT INTO public.sources (id, link, type) OVERRIDING SYSTEM VALUE VALUES (9, 'https://www.postgresql.org/docs/current/functions-json.html', 'web');
INSERT INTO public.sources (id, link, type) OVERRIDING SYSTEM VALUE VALUES (10, 'https://cs.m.wikipedia.org/wiki/Git', 'web');
INSERT INTO public.sources (id, link, type) OVERRIDING SYSTEM VALUE VALUES (11, 'https://cs.m.wikipedia.org/wiki/Git', 'web');
INSERT INTO public.sources (id, link, type) OVERRIDING SYSTEM VALUE VALUES (12, 'https://cs.m.wikipedia.org/wiki/Git', 'web');
INSERT INTO public.sources (id, link, type) OVERRIDING SYSTEM VALUE VALUES (13, 'https://cs.m.wikipedia.org/wiki/Git', 'web');
INSERT INTO public.sources (id, link, type) OVERRIDING SYSTEM VALUE VALUES (14, 'http://www.psychologie-ovlivnovani.cz/', 'web');

INSERT INTO bulletpoint_referenced_themes(bulletpoint_id, theme_id) VALUES (2, 2);
INSERT INTO bulletpoint_referenced_themes(bulletpoint_id, theme_id) VALUES (5, 4);
INSERT INTO bulletpoint_referenced_themes(bulletpoint_id, theme_id) VALUES (8, 5);
INSERT INTO bulletpoint_referenced_themes(bulletpoint_id, theme_id) VALUES (9, 4);

INSERT INTO public.tags (id, name) OVERRIDING SYSTEM VALUE VALUES (2, 'IT');
INSERT INTO public.tags (id, name) OVERRIDING SYSTEM VALUE VALUES (1, 'programovací jazyk');
INSERT INTO public.tags (id, name) OVERRIDING SYSTEM VALUE VALUES (3, 'Software');
INSERT INTO public.tags (id, name) OVERRIDING SYSTEM VALUE VALUES (4, 'Databáze');
INSERT INTO public.tags (id, name) OVERRIDING SYSTEM VALUE VALUES (5, 'Psychologie');

INSERT INTO public.theme_alternative_names (id, name, theme_id) OVERRIDING SYSTEM VALUE VALUES (1, 'Objektově orientované programování', 2);
INSERT INTO public.theme_alternative_names (id, name, theme_id) OVERRIDING SYSTEM VALUE VALUES (2, 'PG', 3);
INSERT INTO public.theme_alternative_names (id, name, theme_id) OVERRIDING SYSTEM VALUE VALUES (3, 'postgres', 3);
INSERT INTO public.theme_alternative_names (id, name, theme_id) OVERRIDING SYSTEM VALUE VALUES (4, 'OSS', 4);

INSERT INTO public.theme_tags (id, theme_id, tag_id) OVERRIDING SYSTEM VALUE VALUES (3, 2, 1);
INSERT INTO public.theme_tags (id, theme_id, tag_id) OVERRIDING SYSTEM VALUE VALUES (7, 1, 1);
INSERT INTO public.theme_tags (id, theme_id, tag_id) OVERRIDING SYSTEM VALUE VALUES (8, 3, 4);
INSERT INTO public.theme_tags (id, theme_id, tag_id) OVERRIDING SYSTEM VALUE VALUES (9, 4, 3);
INSERT INTO public.theme_tags (id, theme_id, tag_id) OVERRIDING SYSTEM VALUE VALUES (10, 5, 3);
INSERT INTO public.theme_tags (id, theme_id, tag_id) OVERRIDING SYSTEM VALUE VALUES (11, 6, 3);
INSERT INTO public.theme_tags (id, theme_id, tag_id) OVERRIDING SYSTEM VALUE VALUES (12, 7, 5);

INSERT INTO public.user_tag_reputations (id, user_id, tag_id, reputation) OVERRIDING SYSTEM VALUE VALUES (17, 1, 3, 4);
INSERT INTO public.user_tag_reputations (id, user_id, tag_id, reputation) OVERRIDING SYSTEM VALUE VALUES (21, 1, 5, 1);
INSERT INTO public.user_tag_reputations (id, user_id, tag_id, reputation) OVERRIDING SYSTEM VALUE VALUES (1, 1, 1, 0);
INSERT INTO public.user_tag_reputations (id, user_id, tag_id, reputation) OVERRIDING SYSTEM VALUE VALUES (2, 1, 2, 0);

INSERT INTO contributed_bulletpoints (theme_id, source_id, user_id, content) VALUES (1, 3, 2, 'Test');

REFRESH MATERIALIZED VIEW CONCURRENTLY bulletpoint_reputations;
REFRESH MATERIALIZED VIEW CONCURRENTLY starred_themes;