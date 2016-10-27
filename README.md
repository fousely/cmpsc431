Step 1: Dependencies

> sudo apt-get install mysql-server-5.6 apache2 php5-gd libssh2-php


Step 2: MySql

> mysql -u root -p --local-infile

> 	- Enter password. 

> SOURCE CreateStatements.sql;

> SOURCE LoadData.sql;


Step 3: Symlinks

> sudo ln -sr /home/stephen/Desktop/git/web/* /var/www/html/ 

> 	- The first directory is where you git folder is. EX) '{path to git}/git/web/*'


Step 4: Connect

> Open firefox and go to the website '127.0.0.1'

