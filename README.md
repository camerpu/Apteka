# Apteka
Simple app to store informations about pharmacies<br>
Live version: https://system.max-play.pl/<br>
Used technologies: PHP(Symfony), JS(jQuery), TWIG for views.<br>
The table with pagination, filtering/ordering is build with jQuery Datatables.
App has also REST API created automatically with symfony api platform component, you can check the documentation here: https://system.max-play.pl/api
# To install:
git clone https://github.com/camerpu/Apteka/<br>
composer update<br>
[put correct db credentials in .env file]<br>
php bin/console doctrine:schema:create
# Tests
Before tests, run:<br>
<b>php bin/console --env=test doctrine:database:create</b><br>
<b>php bin/console --env=test doctrine:schema:create</b><br>
<b>php bin/console --env=test doctrine:fixtures:load</b> - to load default data for tests into DB<br>
App contains a few simple tests which you can run by: <b>php bin/phpunit</b>
# Postman
In the repo you can find a file <b>Apteka.postman_collection.json</b> to import in Postman and check two simple endpoints.
