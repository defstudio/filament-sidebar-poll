<?php

namespace DefStudio\FilamentSidebarPoll;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use InvalidArgumentException;

class SidebarPoll implements Plugin
{
    protected ?string $pollingInterval = null;

    public function pollingInterval(string $interval): static
    {
        $this->validatePollingInterval($interval);
        $this->pollingInterval = $interval;

        return $this;
    }

    public function getPollingInterval(): string
    {
        $interval = $this->pollingInterval ?? config('sidebar-poll.interval', '10s');

        if (! is_string($interval)) {
            throw new InvalidArgumentException('The sidebar polling interval must be a string.');
        }

        $this->validatePollingInterval($interval);

        return $interval;
    }

    protected function validatePollingInterval(string $interval): void
    {
        if (! preg_match('/^[1-9][0-9]*(ms|s|m)$/D', $interval)) {
            throw new InvalidArgumentException('The sidebar polling interval must be a positive integer followed by ms, s, or m.');
        }
    }

    public function getId(): string
    {
        return 'filament-sidebar-poll';
    }

    public function register(Panel $panel): void
    {
        $panel->renderHook(PanelsRenderHook::SIDEBAR_NAV_END, fn () => view('filament-sidebar-poll::sidebar-polling', [
            'interval' => $this->getPollingInterval(),
        ]));
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
