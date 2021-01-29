echo $HETZNER_SSH_PASSWORD | sudo -kS cp -r dev/round-robin/* /var/www/html/round-robin/

cd /var/www/html/round-robin/

php artisan migrate --force
