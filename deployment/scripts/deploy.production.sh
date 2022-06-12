# copy files to /var/www/
echo $HETZNER_SSH_PASSWORD | sudo -kS cp -r dev/round-robin/* /var/www/html/round-robin/

# fix permissions from sudo copy
echo $HETZNER_SSH_PASSWORD | sudo -kS chown www-data:www-data -R /var/www/html/round-robin/
#
## Copy Supervisor Files
#sudo cp round-robin.server/deployment/config/workers-database.production.conf /etc/supervisor/conf.d/round-robin-com-workers-database-production.conf
#sudo cp round-robin.server/deployment/config/workers-redis.production.conf /etc/supervisor/conf.d/round-robin-com-workers-redis-production.conf
#
# Instruct supervisor to read the new files
echo $HETZNER_SSH_PASSWORD | sudo -kS supervisorctl reread
echo $HETZNER_SSH_PASSWORD | sudo -kS supervisorctl update
echo $HETZNER_SSH_PASSWORD | sudo -kS supervisorctl start all

# navigate to the new files
cd /var/www/html/round-robin/

## remove deployment instructions
#composer dumpautoload

# migrate the database
php artisan migrate --force

# cache the config
php artisan config:cache

# cache the routes
php artisan route:cache

# create storage links
php artisan storage:link

# send restart signal to queue
php artisan queue:restart
