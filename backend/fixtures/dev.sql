INSERT INTO users (username, email, password, role) VALUES ('klapuch', 'klapuchdominik@gmail.com', '$argon2i$v=19$m=131072,t=4,p=4$ZEhjRjVaYzYuYkg2VUQwcQ$EuRHYiI+7NJoVtkcdbPTh4QUHxWw7FOncPphl18ZGq4', 'admin'); -- heslo123
INSERT INTO users (username, email, password, role) VALUES ('test', 'test@gmail.com', '$argon2i$v=19$m=131072,t=4,p=4$ZEhjRjVaYzYuYkg2VUQwcQ$EuRHYiI+7NJoVtkcdbPTh4QUHxWw7FOncPphl18ZGq4', 'member'); -- heslo123
UPDATE access.verification_codes SET used_at = now() WHERE user_id = 1;
UPDATE access.verification_codes SET used_at = now() WHERE user_id = 2;

INSERT INTO "references" (url) VALUES ('https://wikipedia.org/wiki/PHP');
INSERT INTO sources (link, type) VALUES ('https://wikipedia.org/wiki/PHP', 'web');

INSERT INTO tags (name) VALUES ('programming language');
INSERT INTO tags (name) VALUES ('IT');

INSERT INTO themes (name, reference_id, user_id) VALUES ('PHP', 1, 1);

INSERT INTO theme_tags (theme_id, tag_id) VALUES (1, 1), (1, 2);

INSERT INTO bulletpoints (theme_id, source_id, user_id, content) VALUES (1, 1, 1, 'Strmá křivka učení');
INSERT INTO bulletpoints (theme_id, source_id, user_id, content) VALUES (1, 1, 1, 'Datové typy');

INSERT INTO contributed_bulletpoints (theme_id, source_id, user_id, content) VALUES (1, 1, 2, 'Test');