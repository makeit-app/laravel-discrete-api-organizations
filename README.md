# Laravel Discrete API Organizations

Laravel API for discrete Frontend. Organizations Version.
Inscludes: organization->workspaces management

## Requirements

`composer require make-it-app/laravel-discrete-api-base` - Laravel API for discrete Frontend. Base Version.
Just visit the https://github.com/makeit-app/laravel-discrete-api-base and follow installation instructions.<br>
This package must be installed and configured manually AS ROOT PACKAGE before this one for the Laravel project, with all due care.

## Installation

`composer require make-it-app/laravel-discrete-api-organizations`

## Setup

`php artisan vendor:publish --provider="MakeIT\\DiscreteApi\\Organizations\\Providers\\DiscreteApiOrganizationsServiceProvider" --tag="install"`

**THEN** (soon)

`php artisan makeit:discreteapi:organizations:install` - run the installer and follow the instructions

## Migrate

`php artisan migrate`

## Routes

- GET    `api/user/organizations/current` - get currently selected organization
- PUT    `api/user/organizations/current` - update currently selected organization
- DELETE `api/user/organizations/current` - delete currently selected organization
- PUT    `api/user/organizations/switch` - switch to a selected organization
- GET    `api/user/organizations/list` - list all organizations where user is registered
- GET    `api/user/organizations/workspaces/current` - get currently selected workspace with their content
- PUT    `api/user/organizations/workspaces/current` - update currently selected workspace
- DELETE `api/user/organizations/workspaces/current` - delete currently selected workspace
- PUT    `api/user/organizations/workspaces/switch` - switch to a selected workspace
- GET    `api/user/organizations/list` - list all organization workspaces
- GET    `api/user/organizations/members` - members list and what they do
- POST   `api/user/organizations/members/invite` - invite member(s) to the currently selected organization
- GET    `api/user/organizations/members/accept/{USER_UUID}/{ORGANIZATION_UUID}?signature={SIGNATURE}` - signed:relative url to accept membership
- GET    `api/user/organizations/members/decline/{USER_UUID}/{ORGANIZATION_UUID}?signature={SIGNATURE}` - signed:relative url to decline membership
- PUT    `api/user/organizations/members` - update member in the currently selected organization
- DELETE `api/user/organizations/members/{UUID}` - kick member from the currently selected organization

Getting organizations data via middleware without content (skeleton)<br>
`\MakeIT\DiscreteApi\Organizations\Http\Middleware\PreloadUserOrganizations::class`
this middleware will append to the `request()->user()` structured data via `->load()` method.
