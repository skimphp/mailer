<?php declare(strict_types=1);

namespace Skim\Mailer;

use Skim\Core\App;
use Skim\Ext\Extension;
use Skim\Mailer\Cli\EtherealTestCommand;
use Skim\Mailer\Cli\MailTestCommand;

final class MailerExtension extends Extension {
    public string $name = 'mailer';
    public string $version = '1.0.0';
    public string $description = 'Symfony Mailer integration for SKIM applications.';

    public array $provides = ['mailer'];
    public array $capabilities = ['email'];

    public function register(App $app): void {
        $app->bind(MailerService::class, fn(): MailerService => new MailerService());
        $app->bind('mailer', fn(App $app): MailerService => $app->make(MailerService::class));
    }

    public function boot(App $app): void {
        $app->router->command('mail:test', [MailTestCommand::class, 'handle']);
        $app->router->command('mail:preview-test', [EtherealTestCommand::class, 'handle']);
    }

    public function config(): array {
        return require dirname(__DIR__) . '/config/skim_mailer.php';
    }

    public function commands(): array {
        return [
            'mail:test'         => MailTestCommand::class,
            'mail:preview-test' => EtherealTestCommand::class,
        ];
    }

    public function envKeys(): array {
        return [
            'MAIL_DRIVER',
            'MAIL_FROM',
            'MAIL_FROM_NAME',
            'MAIL_HOST',
            'MAIL_PORT',
            'MAIL_ENCRYPTION',
            'MAIL_USERNAME',
            'MAIL_PASSWORD',
            'ETHEREAL_USERNAME',
            'ETHEREAL_PASSWORD',
            'ETHEREAL_FROM',
            'ETHEREAL_FROM_NAME',
            'MAILGUN_KEY',
            'MAILGUN_DOMAIN',
            'POSTMARK_TOKEN',
            'AWS_ACCESS_KEY_ID',
            'AWS_SECRET_ACCESS_KEY',
            'AWS_DEFAULT_REGION',
            'SENDGRID_KEY',
        ];
    }

    public function postInstall(): array {
        return [
            'Edit config/skim_mailer.php to set your transport driver',
            'Run: php skim mail:test you@example.com',
            'Run: php skim mail:preview-test you@example.com to preview with Ethereal',
        ];
    }

    public static function manifest(): array {
        return [
            'name'        => 'mailer',
            'version'     => '1.0.0',
            'description' => 'Symfony Mailer integration for SKIM applications.',
            'requires'    => [],
            'provides'    => ['mailer'],
            'conflicts'   => [],
            'capabilities' => [
                'email' => [
                    'drivers' => ['smtp', 'mailgun', 'postmark', 'ses', 'sendgrid', 'array'],
                ],
            ],
            'migrations'   => false,
            'commands'     => ['mail:test', 'mail:preview-test'],
            'env_keys'     => [
                'MAIL_DRIVER',
                'MAIL_FROM',
                'MAIL_FROM_NAME',
                'MAIL_HOST',
                'MAIL_PORT',
                'MAIL_ENCRYPTION',
                'MAIL_USERNAME',
                'MAIL_PASSWORD',
                'ETHEREAL_USERNAME',
                'ETHEREAL_PASSWORD',
                'ETHEREAL_FROM',
                'ETHEREAL_FROM_NAME',
                'MAILGUN_KEY',
                'MAILGUN_DOMAIN',
                'POSTMARK_TOKEN',
                'AWS_ACCESS_KEY_ID',
                'AWS_SECRET_ACCESS_KEY',
                'AWS_DEFAULT_REGION',
                'SENDGRID_KEY',
            ],
            'post_install' => [
                'Edit config/skim_mailer.php to set your transport driver',
                'Run: php skim mail:test you@example.com',
                'Run: php skim mail:preview-test you@example.com to preview with Ethereal',
            ],
        ];
    }
}
