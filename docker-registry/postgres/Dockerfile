FROM postgres:12-alpine

RUN apk add --no-cache supervisor make git findutils gcc libc-dev icu-dev
RUN mkdir /etc/supervisor.d

RUN rm -rf /var/cache/apk/*

ENTRYPOINT ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
