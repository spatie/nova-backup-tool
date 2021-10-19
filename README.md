# A Laravel Nova tool to backup your application

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/nova-backup-tool.svg?style=flat-square)](https://packagist.org/packages/spatie/nova-backup-tool)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/nova-backup-tool.svg?style=flat-square)](https://packagist.org/packages/spatie/nova-backup-tool)

This [Nova](https://nova.laravel.com) tool lets you:
- list all backups
- create a new backup
- download a backup
- delete a backup

Behind the scenes [spatie/laravel-backup](https://docs.spatie.be/laravel-backup) is used.

![screenshot of the backup tool](https://spatie.github.io/nova-backup-tool/screenshot.png)

You can see the tool in action in [this video on YouTube](https://www.youtube.com/watch?v=9wSi2ByavX8).

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/nova-backup-tool.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/nova-backup-tool)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Requirements

Make sure you meet [the requirements for installing spatie/laravel-backup](https://docs.spatie.be/laravel-backup/v6/requirements).

## Installation

First you must install [spatie/laravel-backup](https://docs.spatie.be/laravel-backup) into your Laravel app. The installation instructions are [here](https://docs.spatie.be/laravel-backup/v6/installation-and-setup). When successfull running `php artisan backup:run` on the terminal should create a backup and `php artisan backup:list` should return a list with an overview of all backup disks.

You can install the nova tool in to a Laravel app that uses [Nova](https://nova.laravel.com) via composer:

```bash
composer require spatie/nova-backup-tool
```

Next up, you must register the tool with Nova. This is typically done in the `tools` method of the `NovaServiceProvider`.

```php
// in app/Providers/NovaServiceProvider.php

// ...

public function tools()
{
    return [
        // ...
        new \Spatie\BackupTool\BackupTool(),
    ];
}
```

Finally you should setup [a queue](https://laravel.com/docs/master/queues). This tool doesn't care what kind of queue as long as you don't use the `sync` driver.

## Configuration

You can optionally publish the config file with:

```bash
php artisan vendor:publish --provider="Spatie\BackupTool\BackupToolServiceProvider" --tag="config"
```

This is the contents of the published config file:

```php
<?php

return [
    /*
     * Enable or disable backup tool polling.
     */
    'polling' => true,

    /*
     * Interval seconds between polling requests.
     */
    'polling_interval' => 1,

    /*
     * Queue to use for the jobs to run through.
     */
    'queue' => null,

    /*
     * The time at which the URL should expire. 
     * This can be a PHP DateTime object, set to `null` to disable creating a temporary URL
     */
    'temporary_url_expiration' => null, // now()->addDay()
];
```

## Usage

Click on the "Backups" menu item in your Nova app to see the backup tool.

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Postcardware

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Kruikstraat 22, 2018 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
