<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->applyMailSettings();
    }

    /**
     * Se in storage/app/settings.json sono configurati SMTP host + from address,
     * sovrascrive a runtime la config mail di Laravel (sopra l'env).
     */
    protected function applyMailSettings(): void
    {
        $path = storage_path('app/settings.json');
        if (!is_file($path)) {
            return;
        }

        $settings = json_decode(@file_get_contents($path), true);
        if (!is_array($settings)) {
            return;
        }

        $host = trim($settings['smtp_host'] ?? '');
        $fromAddress = trim($settings['mail_from_address'] ?? '');
        $fromName = trim($settings['mail_from_name'] ?? '');

        if ($host !== '') {
            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp.host', $host);
            Config::set('mail.mailers.smtp.port', (int) ($settings['smtp_port'] ?? 587));
            Config::set('mail.mailers.smtp.username', $settings['smtp_username'] ?? null);
            Config::set('mail.mailers.smtp.password', $settings['smtp_password'] ?? null);
            $enc = $settings['smtp_encryption'] ?? 'tls';
            Config::set('mail.mailers.smtp.encryption', $enc === '' ? null : $enc);
            Config::set('mail.mailers.smtp.scheme', $enc === 'ssl' ? 'smtps' : null);
        }

        if ($fromAddress !== '') {
            Config::set('mail.from.address', $fromAddress);
        }
        if ($fromName !== '') {
            Config::set('mail.from.name', $fromName);
        }
    }
}
