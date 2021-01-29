cp -r dev/round-robin/* /var/www/dev/html/round-robin/

cd /var/www/dev/html/round-robin/

php artisan migrate --force

php artisan config:cache

php artisan route:cache

php artisan storage:link
