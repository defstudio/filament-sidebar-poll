<?php

namespace DefStudio\FilamentSidebarPoll;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\View\PanelsRenderHook;

class FilamentSidebarPollPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-sidebar-poll';
    }

    public function register(Panel $panel): void
    {
        $panel->renderHook(PanelsRenderHook::SIDEBAR_NAV_END, fn () => view('filament-sidebar-poll::sidebar-polling'));
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
