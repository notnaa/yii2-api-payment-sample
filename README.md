# Api payment sample project
## Requirements

* php 7.2
* mySql 5.7
* nginx + php-fpm

## Deploy

* config db connects (create config/db.php by example of config/db.example)
* initialization:
```
composer install
php init
php yii migrate
```

## NGINX

```
server {
  charset utf-8;

  listen 80;

  server_name api-payment-sample.loc;

  root /*path-to-project*/yii2-api-payment-sample/web;
  index index.php;

  access_log /var/log/nginx/yii2-api-payment-sample.loc.access.log;
  error_log /var/log/nginx/yii2-api-payment-sample.loc.error.log;

  include /etc/nginx/common/cache;
  include /etc/nginx/common/rewrite;
  include /etc/nginx/common/deny;
  include /etc/nginx/common/phpmyadmin;
}
```
