FROM postgres:11-alpine

RUN apk add --no-cache supervisor make git findutils gcc libc-dev icu-dev
RUN mkdir /etc/supervisor.d
COPY cron.ini /etc/supervisor.d/cron.ini

COPY postgresql.dev.conf /etc/postgresql.conf

COPY .bash_aliases /root/.bash_aliases
COPY .psqlrc /root/.psqlrc

RUN echo '. /root/.bash_aliases' >> ~/.bashrc

COPY disconnect /usr/bin/disconnect
COPY import /usr/bin/import
COPY test_import /usr/bin/test_import
COPY remove_tests /usr/bin/remove_tests

RUN chmod +x /usr/bin/disconnect
RUN chmod +x /usr/bin/import
RUN chmod +x /usr/bin/test_import
RUN chmod +x /usr/bin/remove_tests

# CRON
ADD crontab /crontab
RUN /usr/bin/crontab /crontab

# PLPGSQL_CHECK EXTENSION
COPY plpgsql_check/plpgsql_check.so /usr/local/lib/postgresql/plpgsql_check.so
COPY plpgsql_check/plpgsql_check.control /usr/local/share/postgresql/extension/plpgsql_check.control
COPY plpgsql_check/plpgsql_check--1.6.sql /usr/local/share/postgresql/extension/plpgsql_check--1.6.sql
RUN chmod 0644 /usr/local/lib/postgresql/plpgsql_check.so
RUN chmod 0644 /usr/local/share/postgresql/extension/plpgsql_check.control
RUN chmod 0644 /usr/local/share/postgresql/extension/plpgsql_check--1.6.sql

RUN rm -rf /var/cache/apk/*

ENTRYPOINT ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
