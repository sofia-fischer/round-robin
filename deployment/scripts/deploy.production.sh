echo $PASSWORD | sudo -kS cp -r dev/round-robin/* /var/www/html/round-robin/

cd /var/www/html/round-robin/

php artisan migrate --force

php artisan config:cache

php artisan route:cache

php artisan storage:link
