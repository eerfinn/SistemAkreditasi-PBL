<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Set app name
        Config::set('app.name', 'SistemAkreditasi');
        
        // Uncomment this section for development with log driver
        // Config::set('mail.default', 'log');
        
        // Use SMTP for Gmail - Override ALL mail settings to ensure no conflicts
        Config::set('mail.default', 'smtp');
        
        // Override SMTP settings
        Config::set('mail.mailers.smtp.transport', 'smtp');
        Config::set('mail.mailers.smtp.host', 'smtp.gmail.com');
        Config::set('mail.mailers.smtp.port', 587);
        Config::set('mail.mailers.smtp.encryption', 'tls');
        Config::set('mail.mailers.smtp.username', 'sistemakreditasi@gmail.com');
        Config::set('mail.mailers.smtp.password', 'epag eihb rglh tzgo');
        
        // Override from address
        Config::set('mail.from.address', 'sistemakreditasi@gmail.com');
        Config::set('mail.from.name', 'SistemAkreditasi');
        
        // Logging mail configuration for debugging
        Log::info('Mail configuration loaded', [
            'driver' => Config::get('mail.default'),
            'host' => Config::get('mail.mailers.smtp.host'),
            'port' => Config::get('mail.mailers.smtp.port'),
            'username' => Config::get('mail.mailers.smtp.username'),
            'from_address' => Config::get('mail.from.address'),
        ]);
    }
} 