<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Console;

use Illuminate\Console\Command;

/**
 * Artisan command to install and set up the Laravel Daraja package.
 */
class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'mpesa:install';

    /**
     * The console command description.
     */
    protected $description = 'Install the Laravel Daraja M-Pesa package';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('');
        $this->info('  ╔══════════════════════════════════════╗');
        $this->info('  ║     Laravel Daraja M-Pesa Package    ║');
        $this->info('  ║            Installation              ║');
        $this->info('  ╚══════════════════════════════════════╝');
        $this->info('');

        // Publish configuration
        $this->info('Publishing configuration...');
        $this->call('vendor:publish', [
            '--tag' => 'mpesa-config',
            '--force' => false,
        ]);
        $this->info('✓ Configuration published to config/mpesa.php');

        // Publish migrations
        if ($this->confirm('Would you like to publish the database migrations?', true)) {
            $this->call('vendor:publish', [
                '--tag' => 'mpesa-migrations',
                '--force' => false,
            ]);
            $this->info('✓ Migrations published');
        }

        $this->info('');
        $this->info('Verifying Configuration...');

        $missingKeys = [];
        $keys = [
            'MPESA_CONSUMER_KEY' => config('mpesa.consumer_key'),
            'MPESA_CONSUMER_SECRET' => config('mpesa.consumer_secret'),
            'MPESA_PASSKEY' => config('mpesa.passkey'),
            'MPESA_SHORTCODE' => config('mpesa.shortcode'),
        ];

        foreach ($keys as $envKey => $value) {
            if (empty($value)) {
                $missingKeys[] = $envKey;
                $this->line("  <fg=red>✗ {$envKey} is missing</>");
            } else {
                $this->line("  <fg=green>✓ {$envKey} is set</>");
            }
        }

        if (count($missingKeys) > 0) {
            $this->info('');
            $this->warn('⚠️  Your Daraja configuration is incomplete.');
            $this->line('Please add the missing keys to your .env file before making API calls.');
        } else {
            $this->info('');
            $this->info('✨ Your configuration looks good!');
        }

        $this->info('');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('');
        $this->info('  Next steps:');
        $this->info('');
        $this->info('  1. Add your Daraja API credentials to .env:');
        $this->info('');
        $this->line('     MPESA_ENVIRONMENT=sandbox');
        $this->line('     MPESA_CONSUMER_KEY=your_consumer_key');
        $this->line('     MPESA_CONSUMER_SECRET=your_consumer_secret');
        $this->line('     MPESA_SHORTCODE=174379');
        $this->line('     MPESA_PASSKEY=your_passkey');
        $this->line('     MPESA_STK_CALLBACK_URL=https://your-app.com/api/mpesa/webhooks/stk-push');
        $this->info('');
        $this->info('  2. Get your credentials from:');
        $this->info('     https://developer.safaricom.co.ke');
        $this->info('');
        $this->info('  3. Run migrations (if published):');
        $this->line('     php artisan migrate');
        $this->info('');
        $this->info('  4. Test your integration:');
        $this->line("     Mpesa::stkPush()->phone('0712345678')->amount(1)->reference('TEST')->push();");
        $this->info('');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('');
        $this->info('  Installation complete! Happy coding 🚀');
        $this->info('');

        return self::SUCCESS;
    }
}
