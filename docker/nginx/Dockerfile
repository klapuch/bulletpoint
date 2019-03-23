FROM nginx:alpine

COPY nginx.dev.conf /etc/nginx/nginx.conf

COPY api.dev.conf /etc/nginx/sites-available/api.conf
COPY api.dev.conf /etc/nginx/sites-enabled/api.conf

COPY static.dev.conf /etc/nginx/sites-available/static.conf
COPY static.dev.conf /etc/nginx/sites-enabled/static.conf

COPY php.conf /etc/nginx/php.conf
COPY routes.conf /etc/nginx/routes.conf
COPY preflight.conf /etc/nginx/preflight.conf
COPY preflight_headers.conf /etc/nginx/preflight_headers.conf
COPY security_headers.conf /etc/nginx/security_headers.conf

COPY ssl/bulletpoint.localhost.crt /etc/ssl/certs/bulletpoint.localhost.crt
COPY ssl/bulletpoint.localhost.key /etc/ssl/private/bulletpoint.localhost.key
