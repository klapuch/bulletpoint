FROM debian:latest

RUN apt-get update -qq && apt-get install -y sudo libcurl4-openssl-dev gnupg curl \
    && apt-get remove nodejs \
        && curl -sL https://deb.nodesource.com/setup_10.x | sudo -E bash - \
        && apt-get install -y nodejs \
        && ln -sf /usr/bin/node /usr/bin/nodejs \
    && curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - \
        && echo "deb https://dl.yarnpkg.com/debian/ stable main" | sudo tee /etc/apt/sources.list.d/yarn.list \
        && apt-get update \
        && apt-get install yarn \
    && yarn global add create-react-app \
    && yarn install \
    && apt-get clean
