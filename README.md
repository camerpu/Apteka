# Apteka
Simple app to store informations about pharmacies<br>
Live version: https://system.max-play.pl/<br>
Used technologies: PHP(Symfony), JS(jQuery), TWIG for views.<br>
The table with pagination, filtering/ordering is build with jQuery Datatables.
App has also REST API created automatically with symfony api platform component, you can check the documentation here: https://system.max-play.pl/api
# To install manually:
git clone https://github.com/camerpu/Apteka/<br>
cd Apteka<br>
cd src && composer update<br>
[put correct db credentials in .env file]<br>
cd src && php bin/console doctrine:schema:create
# To install with docker:
git clone https://github.com/camerpu/Apteka/<br>
cd Apteka/docker<br>
docker-compose up -d<br>
docker-compose run php-fpm php bin/console doctrine:schema:create<br>
docker-compose run php-fpm mkdir /var/www/assets/uploads<br>
docker-compose run php-fpm chmod 777 /var/www/assets/uploads/<br>

# Tests
Before tests, run:<br>
<b>php bin/console --env=test doctrine:database:create</b><br>
<b>php bin/console --env=test doctrine:schema:create</b><br>
<b>php bin/console --env=test doctrine:fixtures:load</b> - to load default data for tests into DB<br>
App contains a few simple tests which you can run by: <b>php bin/phpunit</b>
# Postman
In the repo you can find a file <b>Apteka.postman_collection.json</b> to import in Postman and check two simple endpoints.
