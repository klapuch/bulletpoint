FROM klapuch/bulletpoint:postgres

COPY .psqlrc /root/.psqlrc

COPY cron.ini /etc/supervisor.d/cron.ini

COPY postgresql.dev.conf /etc/postgresql.conf

# CRON
ADD crontab /crontab
RUN /usr/bin/crontab /crontab

COPY disconnect /usr/bin/disconnect
COPY import /usr/bin/import
COPY test_import /usr/bin/test_import
COPY remove_tests /usr/bin/remove_tests

RUN chmod +x /usr/bin/disconnect
RUN chmod +x /usr/bin/import
RUN chmod +x /usr/bin/test_import
RUN chmod +x /usr/bin/remove_tests

# PLPGSQL_CHECK EXTENSION
RUN git clone https://github.com/okbob/plpgsql_check.git
WORKDIR plpgsql_check
RUN make clean
RUN make all
RUN make install

RUN /bin/mkdir -p '/usr/local/lib/postgresql'
RUN /bin/mkdir -p '/usr/local/share/postgresql/extension'
RUN /bin/mkdir -p '/usr/local/share/postgresql/extension'
RUN /usr/bin/install -c -m 755  plpgsql_check.so '/usr/local/lib/postgresql/plpgsql_check.so'
RUN /usr/bin/install -c -m 644 .//plpgsql_check.control '/usr/local/share/postgresql/extension/'
RUN /usr/bin/install -c -m 644 .//plpgsql_check--1.7.sql  '/usr/local/share/postgresql/extension/'
