FROM nginx:alpine

COPY nginx.dev.conf /etc/nginx/nginx.conf

COPY bulletpoint.dev.conf /etc/nginx/sites-available/bulletpoint.conf
COPY bulletpoint.dev.conf /etc/nginx/sites-enabled/bulletpoint.conf

COPY php.conf /etc/nginx/php.conf
COPY routes.conf /etc/nginx/routes.conf
COPY preflight.conf /etc/nginx/preflight.conf
COPY preflight_headers.conf /etc/nginx/preflight_headers.conf
COPY security_headers.conf /etc/nginx/security_headers.conf

COPY ssl/bulletpoint.localhost.crt /etc/ssl/certs/bulletpoint.localhost.crt
COPY ssl/bulletpoint.localhost.key /etc/ssl/private/bulletpoint.localhost.key
