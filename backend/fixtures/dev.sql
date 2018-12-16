INSERT INTO users (email, password, role) VALUES ('klapuchdominik@gmail.com', '251d541f1195f4b4f76ff37d71dd97d797694054c9b9f90602b717fd9e4d47a5f18eeaa099380790abc03093d0f22bb24e1b7a90145c3f4dde96206ffc8559b4ac4b88324f8bf35cb2ab37a620a0ade6', 'member'); -- heslo123
UPDATE access.verification_codes SET used_at = now() WHERE user_id = 1;

INSERT INTO "references" (url) VALUES ('https://www.wikipedia.com');
INSERT INTO sources (link, type) VALUES ('https://www.wikipedia.com/php', 'web');

INSERT INTO tags (name) VALUES ('programming language');
INSERT INTO tags (name) VALUES ('IT');

INSERT INTO themes (name, reference_id, user_id) VALUES ('PHP', 1, 1);

INSERT INTO theme_tags (theme_id, tag_id) VALUES (1, 1), (1, 2);

INSERT INTO bulletpoints (theme_id, source_id, user_id, content) VALUES (1, 1, 1, 'Good');