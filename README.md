1. git clone 
2. docker-compose build
3. docker-compose start
4. docker-compose exec app php composer.phar install   
5. docker-compose exec app bin/console doctrine:migration:migrate --no-interaction