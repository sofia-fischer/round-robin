echo $HETZNER_SSH_PASSWORD | sudo -kS cp -r dev/round-robin/* /var/www/html/round-robin/

cd /var/www/html/round-robin/

echo $HETZNER_SSH_PASSWORD | sudo -kS chown -R $HETZNER_SSH_USERNAME:$HETZNER_SSH_USERNAME /var/www/html/round-robin

php artisan migrate --force

php artisan config:cache

echo $HETZNER_SSH_PASSWORD | sudo -kS chown -R www-data:www-data /var/www/html/round-robin
