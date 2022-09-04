FROM gitlab.sunadv.ru:5050/development/nadomy.rf:base

COPY . /var/www/html

RUN cd /var/www/html && composer install --no-scripts -n
RUN cd /var/www/html && yarn install && yarn build

RUN mkdir /var/www/html/var
RUN chmod -R 0777 /var/www/html/var

COPY ./docker/000-default.conf /etc/apache2/sites-enabled/000-default.conf

WORKDIR /var/www/html/public

