GitHub PHP Project Finder (Symfony version)
Author: Erik Johnson

## Instructions for Setting Up 
In order to get this site to work in your local environment, follow these instructions: 

1. Copy this repository into your web root directory. You may need to update the server configuration files and set the "public" folder as DocumentRoot (located in httpd.conf or httpd-vhosts.conf, if using Apache).

2. Database setup: this will require importing the .sql file found within the root of the repository. Look for "project_finder_v2_symfony.sql" file. 
Importing this file will create a test database in your local MySQL server named "project_finder_erikjohnson_symfony", as well as the tables to manage 
the application's repository and Doctrine migration data. These tables may include sample data already, but feel free to truncate them and start over. Passwords may 
need to be adjusted to your local MySQL settings. To adjust the credentials, edit the DATABASE_URL property contained in the ".env" or ".env.local" settings file, 
located at the root directory. 

3. To successfully call the GitHub API, please ensure that you have the cURL extension enabled in your php.ini file. 

4. The application should now load by navigating to http://localhost/index.php in your browser. 

Please contact me if you have questions, or need assistance!
