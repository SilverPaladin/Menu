composer selfupdate
composer update --with-all-dependencies
npm install -g npm@latest
npm update
npm run build
composer test && php artisan optimize
