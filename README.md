# Laravel React Training

## Laravel

### Installation on Windows 11 using powershell

* Install PHP, Composer and Laravel

 ```
 # Run as administrator...
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.5'))
```

* You have PHP and Composer 

```
composer global require laravel/installer
```

* Create a new Laravel application
```
laravel new erp-laravel-api
```
* Check PHP and Composer version
```
php -v
composer -v
```
* Create workspace directory
```
mkdir training-workspace
cd training-workspace
```
* Create a Laravel application
```
composer create-project laravel/laravel erp-laravel-api "^13.0"
cd erp-laravel-api
```
* Install API
```
php artisan install:api
php artisan migrate
php artisan serve
php artisan --version
php artisan route:list --path=api
```
* Authentication
```
  $user = App\Models\User::factory()->create([
      'name' => 'HR Manager',
      'email' => 'manager@company.test',
      'password' => 'password',
  ]);
  
  $user->createToken(
      'postman',
      ['employees:read', 'employees:write', 'employees:export']
  )->plainTextToken;

```

## React

### Installation on Windows 11 using powerpoint

* Get NodeJS
```
winget install --id OpenJS.NodeJS.LTS -e
```
* Install Vite creator 
```
npm install --global create-vite
```
* Create a React app
```
npm create vite@latest erp-react-frontend -- --template react
```

## Microservices

### Create mocroservice basic apps
```
mkdir microservices
cd microservices
 
laravel new employee-service
laravel new payroll-service

mkdir docker
mkdir oracle
```
* Build Docker
```
docker compose build
```
* Run docker 
```
docker compose up -d
```
#### Employee Service App

* Start Tinker in employee app 
 ```
 docker compose exec employee-service php artisan tinker
 ```
* Test database connection
```
DB::connection()->getPdo();
```
* Run sample query
```
DB::select('SELECT 1 FROM DUAL');
```
* Exit from the Tinker
```
exit
```
* Create Employee Model and Migration in Employee Service App
```
docker compose exec employee-service php artisan make:model Employee -m
```
* Run the migration
```
docker compose exec employee-service php artisan migrate
```
* Adding a simple API
```
docker compose exec employee-service php artisan make:controller EmployeeController
```
* Installing the API plugin
```
docker compose exec employee-service php artisan install:api
```
#### Payroll Service App

Do the same db connection test as the above

* Create Salary Model and Migration

```
docker compose exec payroll-service php artisan make:model Salary -m
```
```
docker compose exec payroll-service php artisan migrate
```
* Create SalaryController
```
docker compose exec payroll-service php artisan make:controller SalaryController
```

