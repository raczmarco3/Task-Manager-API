# Task-Manager-API

**Description:** Create an API using the Symfony framework (6.3) that allows users to create, modify, and delete tasks. The API should communicate with the client application in JSON format. The application should provide the ability to list, create, modify, and delete tasks.

**Features:**

1. User registration, login and logout.
2. Retrieve tasks in a list, sorted by due date.
3. Create, edit, and delete tasks.
4. Indicate upcoming due dates in the API response.
5. Authentication and authorization for API endpoints.
6. Clear error messages and status codes handling.
7. Documentation of the application using Swagger.

# Installation
1. console: git clone https://github.com/raczmarco3/Task-Manager-API.git
2. console: cd Task-Manager-API
3. console: cd api
4. console: composer install
5. edit your database connection data in .env
6. console: php bin/console doctrine:database:create

# Requirements
- Composer
- MySQL or MariaDB
- PHP >= 8.1