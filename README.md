# Laravel Discrete API Organizations

Laravel API for discrete Frontend. Organizations Version.
Inscludes: organization->workspaces management

## Requirements

`composer require make-it-app/laravel-user-roles` - User's Role Subsystem.<br>
Just visit the https://github.com/makeit-app/laravel-user-roles and follow installation instructions.<br>
This package must be installed and configured manually AS ROOT PACKAGE before this one for the Laravel project, with all due care.

`composer require make-it-app/laravel-discrete-api-base` - Laravel API for discrete Frontend. Base Version.
Just visit the https://github.com/makeit-app/laravel-discrete-api-base and follow installation instructions.<br>
This package must be installed and configured manually AS ROOT PACKAGE before this one for the Laravel project, with all due care.

`composer require make-it-app/laravel-discrete-api-profile` - Laravel API for discrete Frontend. Profile Version.
Just visit the https://github.com/makeit-app/laravel-discrete-api-profile and follow installation instructions.<br>
This package must be installed and configured manually AS ROOT PACKAGE before this one for the Laravel project, with all due care.

## Installation

`composer require make-it-app/laravel-discrete-api-organizations`

## Setup

`php artisan vendor:publish --provider="MakeIT\\DiscreteApi\\Organizations\\Providers\\DiscreteApiOrganizationsServiceProvider" --tag="migrations"` - if you plan to modify migrations
`php artisan vendor:publish --provider="MakeIT\\DiscreteApi\\Organizations\\Providers\\DiscreteApiOrganizationsServiceProvider" --tag="lang"` - if you plan modyfy localization files

**THEN**

`php artisan makeit:discreteapi:organizations:install` - run the installer and follow the instructions

## Migrate

`php artisan migrate`

## Routes

- GET `api/user/organizations/current` - get currently selected organization
- PUT `api/user/organizations/current` - update currently selected organization
- DELETE `api/user/organizations/current` - delete currently selected organization
- PUT `api/user/organizations/switch` - switch to the user to the organization
- PUT `api/user/organizations/invite` - invite a users to the organization
- GET `api/user/organizations/list` - list all organizations where user is registered

Getting organizations data via middleware `src/Http/Middleware/PreloadUserOrganizations.php` at `api/user` route
