How to run database stuff:

mysql -u root -p --local-infile sanchez

source CreateStatements.sql

source LoadData.sql


Setting up wordpress:

Step 1: Dependencies
> sudo apt-get update
> sudo apt-get install mysql-server-5.6 ftp php5-gd libssh2-php apache2

Step 2: MySql
> mysql -u root -p

	- Enter password.

> CREATE DATABASE wordpress;

> CREATE USER teamsanchez@localhost IDENTIFIED BY 'password';

> GRANT ALL PRIVILEGES ON wordpress.* TO teamsanchez@localhost;

> FLUSH PRIVILEGES;

Step 3: Symlinks

> sudo ln -sr /home/stephen/Desktop/git/wordpress/* /var/www/html/

	- The first directory is where you git folder is. EX) '{path to git}/git/wordpress/*'

Step 4: Permissions and Users
> sudo adduser teamsanchez

	- Make the password 'password'

> Enter your wordpress folder and execute this command 'sudo chown -R www-data:www-data *'

> Enter the /wp-content/uploads/ and execute the same command 'sudo chown -R www-data:www-data *'

Step 5: Connect
> Open firefox and go to the website '127.0.0.1/wp-admin/'

> Sign in with

	- Username: teamsanchez

	- Password: password
