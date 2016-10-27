How to run database stuff:

> mysql -u root -p --local-infile sanchez

> source CreateStatements.sql

> source LoadData.sql


Step 1: Dependencies

> sudo apt-get install mysql-server-5.6 ftp php5-gd libssh2-php apache2


Step 2: MySql

> mysql -u root -p 

> 	- Enter password. 

> CREATE DATABASE wordpress; 

> CREATE USER teamsanchez@localhost IDENTIFIED BY 'password'; 

> GRANT ALL PRIVILEGES ON wordpress.* TO teamsanchez@localhost; 

> FLUSH PRIVILEGES;


Step 3: Symlinks

> sudo ln -sr /home/stephen/Desktop/git/web/* /var/www/html/ 

> 	- The first directory is where you git folder is. EX) '{path to git}/git/wordpress/*'


Step 4: Connect

> Open firefox and go to the website '127.0.0.1'

