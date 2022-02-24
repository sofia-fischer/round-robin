echo $HETZNER_SSH_PASSWORD | sudo -kS cp -r dev/round-robin/* /var/www/html/round-robin/

cd /var/www/html/round-robin/

echo $HETZNER_SSH_PASSWORD | sudo -kS chown -R $HETZNER_SSH_USERNAME:$HETZNER_SSH_USERNAME /var/www/html/round-robin

php artisan migrate --force

php artisan config:cache

php artisan route:clear

php artisan route:cache

php artisan storage:link

php artisan test

echo $HETZNER_SSH_PASSWORD | sudo -kS cp /var/www/html/round-robin/deployment/config/workers-redis.production.conf /etc/supervisor/conf.d/round-robin-workers-redis-production.conf

echo $HETZNER_SSH_PASSWORD | sudo -kS supervisorctl reread
echo $HETZNER_SSH_PASSWORD | sudo -kS supervisorctl update
echo $HETZNER_SSH_PASSWORD | sudo -kS supervisorctl start all

echo $HETZNER_SSH_PASSWORD | sudo -kS chown -R www-data:www-data /var/www/html/round-robin
