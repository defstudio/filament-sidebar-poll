# Filament Sidebar Poll

[![Latest Version on Packagist](https://img.shields.io/packagist/v/defstudio/filament-sidebar-poll.svg?style=flat-square)](https://packagist.org/packages/defstudio/filament-sidebar-poll)
[![Tests](https://img.shields.io/github/actions/workflow/status/defstudio/filament-sidebar-poll/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/defstudio/filament-sidebar-poll/actions/workflows/tests.yml)

Automatically refresh your Filament sidebar so navigation badges and other dynamic navigation content stay up to date. Uses Filament's existing Livewire sidebar, with a default polling interval of 10 seconds.

## Requirements

- PHP 8.2 or later
- Filament 5
- Laravel 11, 12, or 13 (Laravel 13 requires PHP 8.3 or later)

## Installation

```bash
composer require defstudio/filament-sidebar-poll
```

Register the plugin in each panel that should poll:

```php
use DefStudio\FilamentSidebarPoll\SidebarPoll;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // Your existing panel configuration...
        ->plugin(SidebarPoll::make());
}
```

No migrations, assets, custom theme, or build steps are required.

## Configuration

Override the interval for an individual panel:

```php
SidebarPoll::make()->pollingInterval('30s')
```

Intervals must be positive integers followed by `ms`, `s`, or `m`, for example `500ms`, `30s`, or `1m`. Invalid intervals throw an `InvalidArgumentException`.

To change the default for all panels, publish the configuration:

```bash
php artisan vendor:publish --tag="filament-sidebar-poll-config"
```

This creates `config/sidebar-poll.php`:

```php
return [
    'interval' => '10s',
];
```

Per-panel settings take precedence over the configuration file. After changing configuration in production, rebuild your application's configuration cache.

Optionally publish the view:

```bash
php artisan vendor:publish --tag="filament-sidebar-poll-views"
```

## Production considerations

Each open sidebar makes approximately one request per interval. Prefer longer intervals when navigation badge queries are expensive or many users are connected. Livewire automatically reduces polling in background tabs; this plugin preserves that behavior. See the [Livewire polling documentation](https://livewire.laravel.com/docs/4.x/wire-poll).

Use closures for dynamic navigation values so they are recalculated when the sidebar renders. Values cached by your application retain that cache's lifetime.

Polling applies to the sidebar. Panels using top navigation do not have a sidebar to refresh.

## Development

```bash
composer install
composer test
composer analyse
composer test:lint
```

The integration tests render Filament's real sidebar and call its Livewire refresh action, including a changing navigation badge. Browser timers and production load require validation in the consuming application.

## Changelog

See [CHANGELOG](CHANGELOG.md).

## Contributing

See [CONTRIBUTING](.github/CONTRIBUTING.md).

## Security

See [our security policy](.github/SECURITY.md).

## Credits

- [Fabio Ivona](https://github.com/fabio-ivona)
- [All Contributors](../../contributors)

## License

MIT. See [LICENSE](LICENSE.md).
