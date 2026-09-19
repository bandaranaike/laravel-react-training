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
laravel new erp-laravel
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