# Laravel Settings


## Installation
Require this package in your composer.json:

    "yaap/settings": "dev-master"

And add the ServiceProvider to the providers array in app/config/app.php

    'Yaap\Settings\SettingsServiceProvider',

Run migration to create settings table
	'php artisan migrate --packadge="yaap/settings"'

Publish config using artisan CLI (if you wont to cascade default config).
	'php artisan config:publish yaap/settings'



## Usage

##Config

    return array(
        'table' => 'settings',
        'fallback' => true,
    );

##Fallback capability built in.
    // Automatic fallback to Laravel config
    Settings::get('app.locale');


You can also clear the JSON file with the clear command

    clear:      Settings::clear()


## Based on
[Phil-F/Setting](https://github.com/Phil-F/Setting)
