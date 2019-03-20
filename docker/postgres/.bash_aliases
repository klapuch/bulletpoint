function export() {
    pg_dump -s -U postgres bulletpoint > /var/www/bulletpoint/backend/fixtures/schema.sql;
}

alias connect="psql -U postgres -h localhost -d bulletpoint"
