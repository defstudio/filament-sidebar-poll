<?php

use DefStudio\FilamentSidebarPoll\FilamentSidebarPollServiceProvider;
use DefStudio\FilamentSidebarPoll\SidebarPoll;
use Filament\Facades\Filament;
use Filament\Livewire\Sidebar;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

function sidebarPanel(string $id, ?SidebarPoll $plugin = null): Panel
{
    $panel = Panel::make()->id($id)->path($id);
    if ($plugin) {
        $panel->plugin($plugin);
    }
    Filament::registerPanel($panel);
    Filament::setCurrentPanel($panel);
    Filament::bootCurrentPanel();

    return $panel;
}

it('renders polling in the sidebar and refreshes dynamic navigation badges', function () {
    $count = 1;
    $panel = sidebarPanel('admin', SidebarPoll::make());
    $panel->navigationItems([
        NavigationItem::make('Messages')->url('/messages')->badge(function () use (&$count): string {
            return (string) $count;
        }),
    ]);

    $sidebar = Livewire::test(Sidebar::class)
        ->assertSeeHtml('wire:poll.10s="refresh"')
        ->assertSee('Messages')
        ->assertDontSee('42');

    $count = 42;
    $sidebar->call('refresh')->assertSuccessful()->assertSee('42');
});

it('uses the configured default interval', function () {
    config()->set('sidebar-poll.interval', '30s');
    sidebarPanel('admin', SidebarPoll::make());
    Livewire::test(Sidebar::class)->assertSeeHtml('wire:poll.30s="refresh"');
});

it('allows a panel to override the default interval', function () {
    config()->set('sidebar-poll.interval', '30s');
    sidebarPanel('admin', SidebarPoll::make()->pollingInterval('1m'));
    Livewire::test(Sidebar::class)->assertSeeHtml('wire:poll.1m="refresh"');
    expect(SidebarPoll::get()->getPollingInterval())->toBe('1m');
});

it('does not add polling to panels without the plugin', function () {
    sidebarPanel('plain');
    expect(FilamentView::renderHook(PanelsRenderHook::SIDEBAR_NAV_END)->toHtml())->not->toContain('wire:poll');
});

it('accepts supported intervals', function (string $interval) {
    expect(SidebarPoll::make()->pollingInterval($interval)->getPollingInterval())->toBe($interval);
})->with(['500ms', '10s', '1m']);

it('rejects invalid intervals', function (string $interval) {
    SidebarPoll::make()->pollingInterval($interval);
})->with(['', '0s', '-1s', '1.5s', '10', '10s.keep-alive', '10s" onclick="alert(1)'])->throws(InvalidArgumentException::class);

it('validates published configuration', function (mixed $interval) {
    config()->set('sidebar-poll.interval', $interval);
    SidebarPoll::make()->getPollingInterval();
})->with([0, false, '0s'])->throws(InvalidArgumentException::class);

it('registers publishable configuration and views', function () {
    $provider = FilamentSidebarPollServiceProvider::class;
    expect(config('sidebar-poll.interval'))->toBe('10s');
    expect(array_map('realpath', array_keys(ServiceProvider::pathsToPublish($provider, 'filament-sidebar-poll-config'))))
        ->toContain(realpath(__DIR__ . '/../config/sidebar-poll.php'));
    expect(ServiceProvider::pathsToPublish($provider, 'filament-sidebar-poll-views'))
        ->not->toBeEmpty();
});
