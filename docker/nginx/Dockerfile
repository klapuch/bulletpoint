FROM nginx:alpine

COPY nginx.conf /etc/nginx/nginx.conf

COPY bulletpoint.conf /etc/nginx/sites-available/fmf.conf
COPY bulletpoint.conf /etc/nginx/sites-enabled/fmf.conf

COPY php.conf /etc/nginx/php.conf
COPY routes.conf /etc/nginx/routes.conf
COPY preflight.conf /etc/nginx/preflight.conf
COPY preflight_headers.conf /etc/nginx/preflight_headers.conf
COPY security_headers.conf /etc/nginx/security_headers.conf

COPY ssl/bulletpoint.localhost.crt /etc/ssl/certs/fmf.localhost.crt
COPY ssl/bulletpointlocalhost.key /etc/ssl/private/fmf.localhost.key