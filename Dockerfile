FROM ubuntu:22.04

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND noninteractive
ENV TZ=UTC

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update \
    && apt-get install --no-install-recommends -y default-mysql-client ca-certificates curl zip unzip git php8.1-cli php8.1-dev \
       php8.1-pgsql php8.1-sqlite3 php8.1-gd \
       php8.1-curl \
       php8.1-imap php8.1-mysql php8.1-mbstring \
       php8.1-xml php8.1-zip php8.1-bcmath php8.1-soap \
       php8.1-intl php8.1-readline \
       php8.1-ldap php8.1-redis\
       php8.1-memcached php8.1-pcov php8.1-xdebug \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*


# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

EXPOSE 8000

RUN chmod +x ./start.sh

CMD [ "./start.sh" ]