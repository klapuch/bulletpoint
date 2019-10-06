FROM nginx:alpine

COPY nginx.dev.conf /etc/nginx/nginx.conf

COPY sites-enabled/api.dev.conf /etc/nginx/sites-available/api.conf
COPY sites-enabled/api.dev.conf /etc/nginx/sites-enabled/api.conf

COPY sites-enabled/static.dev.conf /etc/nginx/sites-available/static.conf
COPY sites-enabled/static.dev.conf /etc/nginx/sites-enabled/static.conf

COPY snippets/php.conf /etc/nginx/php.conf
COPY snippets/routes.conf /etc/nginx/routes.conf
COPY snippets/preflight.conf /etc/nginx/preflight.conf
COPY snippets/preflight_headers.conf /etc/nginx/preflight_headers.conf
COPY snippets/security_headers.conf /etc/nginx/security_headers.conf

COPY ssl/bulletpoint.localhost.crt /etc/ssl/certs/bulletpoint.localhost.crt
COPY ssl/bulletpoint.localhost.key /etc/ssl/private/bulletpoint.localhost.key
