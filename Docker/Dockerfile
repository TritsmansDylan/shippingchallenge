FROM ubuntu:latest

#timezone
ENV TZ=Europe/Brussels
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

#updates
RUN apt-get update && apt-get upgrade -y

#install lighttpd, php, enable cgi
RUN apt-get install -y \
            lighttpd \
            php \
            php-cgi \
            php-mysql 
RUN lighty-enable-mod fastcgi \
    && lighty-enable-mod fastcgi-php

#reload lighttpd
RUN service lighttpd restart

#copy test files
COPY test.php dbconnection.php dbfetch.php /var/www/html/

RUN chmod +x /var/www/html/test.php \
             /var/www/html/dbconnection.php \
             /var/www/html/dbfetch.php \
          && service lighttpd restart

#open port
EXPOSE 80

#run lighttpd server
CMD ["lighttpd", "-D", "-f","/etc/lighttpd/lighttpd.conf"]
