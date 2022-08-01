# CRM

# Installation

> **Note**
>
> Il est recommandé d'avoir une expérience Linux et MySQL avant de l'installer.

> **Note**
>
> Sachez que les commandes fournies peuvent ne pas convenir à votre installation. Veuillez modifier les utilisateurs, les dossiers et les noms de fichiers pour les commandes correspondant à votre installation.

> **Error**
>
> Attention, le CRM est actuellement en pré-release et contient très peu de fonctionnalité.

## Dependencies

- PHP `7.4` or `8.0` (recommended, `8.1` will not work) with the following extensions: `cli`, `openssl`, `gd`, `mysql`, `PDO`, `mbstring`, `tokenizer`, `bcmath`, `xml` or `dom`, `curl`, `zip`, and `fpm` if you are planning to use NGINX.
- MySQL `5.7.22` or higher (MySQL `8` recommended) **or** MariaDB `10.2` or higher.
- Redis (`redis-server`)
- A web server (Apache, NGINX, Caddy, etc.)
- `curl`
- `tar`
- `unzip`
- `git`
- `composer` v2

### Example Dependency Installation

The commands below are simply an example of how you might install these dependencies. Please consult with your
operating system's package manager to determine the correct packages to install.

```bash
# Add "add-apt-repository" command
apt -y install software-properties-common curl apt-transport-https ca-certificates gnupg
# Add additional repositories for PHP, Redis, and MariaDB
LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php
add-apt-repository -y ppa:chris-lea/redis-server
curl -sS https://downloads.mariadb.com/MariaDB/mariadb_repo_setup | sudo bash
# Update repositories list
apt update
# Add universe repository if you are on Ubuntu 18.04
apt-add-repository universe
# Install Dependencies
apt -y install php8.1 php8.1-{cli,gd,mysql,sqlite,pdo,mbstring,tokenizer,bcmath,xml,fpm,curl,zip} mariadb-server nginx tar unzip git redis-server
```

### Extra Dependency Used on this Dashboard

You need to install this, use the appropriate PHP version (php -v)
Extra dependency used for handling currency's

```bash
apt -y install php8.0-intl
```

### Installing Composer

Composer is a dependency manager for PHP that allows us to ship everything you'll need code wise to operate the Panel. You'll
need composer installed before continuing in this process.

```bash
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
```

## Download Files

The first step in this process is to create the folder where the panel will live and then move ourselves into that
newly created folder. Below is an example of how to perform this operation.

```bash
mkdir -p /var/www/crm && cd /var/www/crm
```

```bash
git clone https://github.com/nicolasbaud/crm.git ./
```

## Basic Setup

Now that all the files have been downloaded we need to configure some core aspects of the Panel.

**You will need a database setup and a database user with the correct permissions created for that database before**
**continuing any further.**

### Database Setup

To make a database and database user, you can follow this guide.
This is for MariaDB. Please change the USE_YOUR_OWN_PASSWORD part to your password. Also, 127.0.0.1 is for localhost. Please have basic knowledge of Linux before attempting this. Use at your own responsibility.

```bash
mysql -u root -p
CREATE DATABASE crm;
CREATE USER 'crmuser'@'127.0.0.1' IDENTIFIED BY 'USE_YOUR_OWN_PASSWORD';
GRANT ALL PRIVILEGES ON crm.* TO 'crmuser'@'127.0.0.1';
FLUSH PRIVILEGES;
EXIT;
```

## Web server Configuration

You should paste the contents of the file below, replacing `<domain>` with your domain name being used in a file called crm.conf and place it in `/etc/nginx/sites-available/`, or — if on CentOS, `/etc/nginx/conf.d/.`

## How to add this config

```
cd /etc/nginx/sites-available/
nano crm.conf
```

### Example Nginx Config

```nginx
server {
        listen 80;
        root /var/www/crm/public;
        index index.php index.html index.htm index.nginx-debian.html;
        server_name YOUR.DOMAIN.COM;
        location / {
                try_files $uri $uri/ /index.php?$query_string;
        }
        location ~ \.php$ {
                include snippets/fastcgi-php.conf;
                fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        }
        location ~ /\.ht {
                deny all;
        }
}
```

### Enable Configuration

The final step is to enable your NGINX configuration and restart it.

```bash
# You do not need to symlink this file if you are using CentOS.
sudo ln -s /etc/nginx/sites-available/crm.conf /etc/nginx/sites-enabled/crm.conf
# Check for nginx errors
sudo nginx -t
# You need to restart nginx regardless of OS. only do this you haven't received any errors
systemctl restart nginx
```

### Adding SSL

There are many ways to add SSL to your site. A simple solution is to use Cert bot from Let’s Encrypt. Cert bot will automatically install the certificates for you and keep your SSL certifications up to date!

```bash
sudo apt update
#install certbot for nginx
sudo apt install -y certbot
sudo apt install -y python3-certbot-nginx
#install certificates
sudo certbot --nginx -d yourdomain.com
```

## Panel Installation

First, we will have to install all composer packages.
For this, navigate into your `/var/www/crm` again and run the following command

```bash
composer install --no-dev --optimize-autoloader
```

### Set Permissions

The last step in the installation process is to set the correct permissions on the Panel files so that the web server can
use them correctly.

```bash
# If using NGINX or Apache (not on CentOS):
chown -R www-data:www-data /var/www/crm/
chmod -R 755 storage/* bootstrap/cache/
# If using NGINX on CentOS:
chown -R nginx:nginx /var/www/crm/
chmod -R 755 storage/* bootstrap/cache/
# If using Apache on CentOS
chown -R apache:apache /var/www/crm/
chmod -R 755 storage/* bootstrap/cache/
****
```

Once this is done, you should be able to access the dashboard via your web browser.

### Running the installer [SOON]

```bash
php artisan crm:install
```

#### Don't forget to complete the steps listed below here.

## Queue Listeners

### Crontab Configuration

The first thing we need to do is create a new cron job that runs every minute to process specific Dashboard tasks such as billing users hourly and suspending unpaid servers. To open the crontab run: `crontab -e` and paste the following configuration into crontab.

```bash
* * * * * php /var/www/crm/artisan schedule:run >> /dev/null 2>&1
```

### Create Queue Worker

Next, you need to create a new systemd worker to keep our queue process running in the background. This queue is responsible for sending emails and handling many other background tasks for the Dashboard.

Create a file called `crm.service` in `/etc/systemd/system` with the contents below.

```bash
# CRM Queue Worker File
# ----------------------------------
[Unit]
Description=CRM Queue Worker
[Service]
# On some systems the user and group might be different.
# Some systems use `apache` or `nginx` as the user and group.
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/crm/artisan queue:work --sleep=3 --tries=3
[Install]
WantedBy=multi-user.target
```

Finally, enable the service and set it to boot on machine start.

```bash
sudo systemctl enable --now crm.service
```
