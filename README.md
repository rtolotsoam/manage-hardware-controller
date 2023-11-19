# manage-hardware-controller
Create an API resource with controller

Step 1:Navigate to the master branch and clone this project.

Step 2:Run composer install.

Step 3:Create a user 'tolotsoa' with the password 'testtolotsoa' in the MySQL database and grant all privileges.

Step 4:Execute the command `php bin/console doctrine:database:create` to create the 'hardware' database.

Step 5:Execute the command `php bin/console doctrine:schema:create` to create the 'equipment' table.

Step 6:Launch the Symfony server with the command `symfony serve`.

Step 7:The API documentation is located in the directory /swagger/swagger.json. Execute command to generate swagger.json `.\vendor\bin\openapi --format json --output .\swagger\swagger.json .\swagger\swagger.php src`

Step 8:Use Postman to test the API or import the Swagger.json file with Swagger UI.

Step 9:Launch unit tests with the command ./vendor/bin/phpunit. Before executing the command, make sure to modify the URL parameter.
