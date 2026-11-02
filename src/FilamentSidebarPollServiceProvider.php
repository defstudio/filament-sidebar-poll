<?php

namespace DefStudio\FilamentSidebarPoll;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentSidebarPollServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-sidebar-poll')
            ->hasConfigFile('sidebar-poll')
            ->hasViews();
    }
}
