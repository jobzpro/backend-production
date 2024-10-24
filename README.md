
# Jobzpro Back-end Guide Installation
​
## Introduction
Jobzpro is a job listing platform that makes finding and managing job applications easier for job seeker and employers. Employers will be able to add listings to the website for job seekers to see and apply to.
​
## Prerequisites
- Laravel Framework 10.48.22
- PHP 8.2.8 (cli) (built: Jul  4 2023 15:53:15) (ZTS Visual C++ 2019 x64)
- Laravel Passport
- Laravel Sanctum
- MySQL
​
​
## Installation
* Clone the repository by using the command git clone `https://github.com/jobzpro/backend-production`
* Go to the root project where all of the files reside and use the command `cd backend-production`
* `composer install` to install the dependencies using composer.
* Copy the `.env.example` file to `.env` and configure environment settings such as database connection details. 

* `php artisan passport:keys` to generate application key.
* `php artisan migrate` to run database migrations.
* `php artisan db:seed` to generate data.
* `php artisan serve` to start development server.
* Access the server by using the url `http://localhost:8000` or the URL specified in your development environment.
​
## Troubleshooting
- Check Server Requirements: Ensure that your server meets the minimum requirements to run Laravel. Verify that you have PHP and other necessary extensions installed at the required versions.
​
- Verify File Permissions: Make sure that the file and folder permissions are correctly set. Directories should typically have 755 (rwxr-xr-x) permissions, and files should have 644 (rw-r--r--) permissions.
​
- Check Laravel Configuration Files: Review the .env file in your Laravel project to ensure that database credentials and other settings are correct. Check if you have set the correct database connection details.
​
- Database Connection: Verify that your database server is running and accessible. Test the database connection credentials in the .env file to ensure they are valid.
​
- Composer Dependencies: Run composer install or composer update to ensure all required dependencies are installed correctly.
​
- Clear Cache: Laravel caches various data for improved performance. Sometimes, stale cache can cause issues. Run `php artisan cache:clear` and `php artisan config:clear` to clear the cache.
​
- Check Logs: Look into Laravel's log files (storage/logs/laravel.log) for any error messages or exceptions. This can provide valuable clues about what might be going wrong.
​
- Debugging: Enable debugging mode in Laravel by setting APP_DEBUG=true in the .env file. This will display detailed error messages on the screen, which can help identify the issue.
​
- HTTPS/SSL: If you're accessing the backend over HTTPS, make sure your SSL certificate is valid and properly configured.
​
- Firewall and Security: Check if there are any firewall or security settings that might be blocking access to the backend.
​
- Cross-Origin Resource Sharing (CORS): If you're accessing the backend from a different domain, ensure that CORS is properly configured to allow requests from the frontend.
​
## List of dependencies used
Unknown
​
## License
Unknown

# Jobzpro Codebase Structure

​

## Description
Jobzpro is a job listing platform that makes finding and managing job applications easier for job seeker and employers. Employers will be able to add listings to the website for job seekers to see and apply to.

## Note
Make sure you are in the [main] branch to see the latest changes.​ To access go to baseurl/nova for example https://jobzpro-api.dev2.koda.ws/nova/dashboards/main.

## Folders/Files
- `/bootstrap` - contains files needed for bootstrapping the Laravel application.
- `/config` - includes all the configuration files for the application.
- `/public` - this is the public root of the application, containing the entry point (`index.php`) and assets.
- `/routes` - includes the routes definition files for the application.
- `/tests` - contains PHPUnit test files.
- `/vendor` - this directory is created by the Composer and contains the application's dependencies.
- `.env` - environment file that stores configuration settings specific to the local environment.
- `.env.example` - a template of the .env file that includes the required configuration variables.
- `artisan` - command-line utility for interacting with the application.
- `composer.json`/`composer.lock` - files related to the Composer which manages the application's dependencies.
    
    ### /app
    
    - `/console` - includes artisan commands.
    - `/exceptions` - contains custom exception classes.
	    - `/controllers` - contains your controller classes.
	    - `/middleware` - contains custom middleware classes.
	    - `/requests` - includes form request validation classes.
    
    ### /database
    
    - `/factories` - includes model factories for generating test data.
    - `/migrations` - contains database migration files.
    - `/seeds` - holds the database seed files.
    
    ### /resources
    
    - `/lang​` - holds language files for internationalization.
    - `/views` - contains the Blade templates for the views.

    ### /storage
    
    - `/app` - used for storing application-generated files.
    - `/framework` - holds generated files used by the framework.
    - `/logs` - contains log files.
