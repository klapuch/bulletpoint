INSERT INTO users (email, password) VALUES ('klapuchdominik@gmail.com', 'heslo1');

INSERT INTO "references" (url, name) VALUES ('https://www.wikipedia.com', 'wikipedia');
INSERT INTO sources (link, type) VALUES ('https://www.wikipedia.com/php', 'web');

INSERT INTO tags (name) VALUES ('programming language');
INSERT INTO tags (name) VALUES ('IT');

INSERT INTO themes (name, reference_id, user_id) VALUES ('PHP', 1, 1);

INSERT INTO theme_tags (theme_id, tag_id) VALUES (1, 1), (1, 2);

INSERT INTO bulletpoints (theme_id, source_id, user_id, text) VALUES (1, 1, 1, 'Good');