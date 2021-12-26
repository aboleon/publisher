<?php

namespace Aboleon\Publisher;

use Aboleon\Framework\Components\MetaTags;
use Aboleon\Publisher\Models\Configs;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Aboleon\Publisher\Components\{
    Layout,
    OrganizerLaunchpad,
    OrganizerNodeFields,
    OrganizerNodeParams,
    OrganizerPage,
    OrganizerReplicable};
use Aboleon\Publisher\Console\Install;

//use Illuminate\Routing\Router;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

    public function boot()
    {
        app()->bind('aboleon_publisher_helpers', function() {
            return new Models\Helpers;
        });

        app()->bind('publisher', function() {
            return new Models\Publisher;
        });

        Cache::rememberForever('publisher_configs', function() {
           return Configs::with('nodes')->get();
        });

        $this->loadViewsFrom(__DIR__ . '/Resources/views', 'aboleon.publisher');
        $this->loadTranslationsFrom(__DIR__ . '/Resources/lang', 'aboleon.publisher');
        $this->loadViewComponentsAs('aboleon.publisher', [
            Layout::class,
            OrganizerLaunchpad::class,
            OrganizerNodeFields::class,
            OrganizerNodeParams::class,
            OrganizerPage::class,
            OrganizerReplicable::class,
            MetaTags::class
        ]);

        if ($this->app->runningInConsole()) {

            $this->publishConfig();
            $this->publishMigrations();
            $this->publishSeeders();
            $this->publishAssets();

            $this->commands([
                Install::class,
            ]);
        }

    }

    private function publishConfig()
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('aboleon_publisher.php'),
        ], 'aboleon-publisher-config');
    }

    private function publishMigrations()
    {
        $this->publishes([
            __DIR__ . '/../database/migrations/01_create_pages_table.php.stub' => database_path('migrations/aboleon/publisher/' . date('Y_m_d') . '_0000001_create_pages_table.php'),
            __DIR__ . '/../database/migrations/02_create_pages_data_table.php.stub' => database_path('migrations/aboleon/publisher/' . date('Y_m_d') . '_0000002_create_pages_data_table.php'),
            __DIR__ . '/../database/migrations/03_create_mails_table.php.stub' => database_path('migrations/aboleon/publisher/' . date('Y_m_d') . '_0000003_create_mails_table.php'),
            __DIR__ . '/../database/migrations/04_create_media_content_table.php.stub' => database_path('migrations/aboleon/publisher/' . date('Y_m_d') . '_0000004_create_media_content_table.php'),
            __DIR__ . '/../database/migrations/05_create_media_content_description_table.php.stub' => database_path('migrations/aboleon/publisher/' . date('Y_m_d') . '_0000005_create_media_content_description_table.php'),
            __DIR__ . '/../database/migrations/06_create_nav_table.php.stub' => database_path('migrations/aboleon/publisher/' . date('Y_m_d') . '_0000006_create_nav_table.php'),
            __DIR__ . '/../database/migrations/07_create_geocode_table.php.stub' => database_path('migrations/aboleon/publisher/' . date('Y_m_d') . '_0000007_create_geocode_table.php'),
            __DIR__ . '/../database/migrations/08_create_custom_content_table.php.stub' => database_path('migrations/aboleon/publisher/' . date('Y_m_d') . '_0000008_create_custom_content_table.php'),
            __DIR__ . '/../database/migrations/09_add_full_text_search.php.stub' => database_path('migrations/aboleon/publisher/' . date('Y_m_d') . '_0000009_add_full_text_search.php'),
            __DIR__ . '/../database/migrations/10_create_promoted_table.php.stub' => database_path('migrations/aboleon/publisher/' . date('Y_m_d') . '_0000010_create_promoted_table.php'),
            __DIR__ . '/../database/migrations/11_create_nav_custom_links.php.stub' => database_path('migrations/aboleon/publisher/' . date('Y_m_d') . '_0000011_create_nav_custom_links.php'),
        ], 'aboleon-publisher-migrations');
    }

    private function publishSeeders()
    {
        $this->publishes([
            __DIR__ . '/../database/seeders/Seeder.php' => database_path('seeders/Aboleon/Publisher/Seeder.php'),
        ], 'aboleon-publisher-seeders');
    }


    private function publishAssets(): void
    {
        $this->publishes([
            __DIR__ . '/../publishables/' => public_path('aboleon/publisher/'),
        ], 'aboleon-publisher-assets');
    }
}