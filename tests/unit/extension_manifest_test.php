<?php declare(strict_types=1);

use Skim\Mailer\MailerExtension;
use Skim\Mailer\Cli\EtherealTestCommand;

it('is inspectable without calling register or boot', function(): void {
    $ext = new MailerExtension();

    expect($ext->name)->toBe('mailer');
    expect($ext->capabilities)->toContain('email');
    expect($ext->config())->not->toBeEmpty();
    expect($ext->commands()['mail:preview-test'])->toBe(EtherealTestCommand::class);
    expect($ext->envKeys())->toContain('ETHEREAL_USERNAME', 'ETHEREAL_PASSWORD');
});

it('provides static manifest without instantiation', function(): void {
    $manifest = MailerExtension::manifest();

    expect($manifest)->not->toBeEmpty();
    expect($manifest['name'])->toBe('mailer');
    expect($manifest['provides'])->toContain('mailer');
    expect($manifest['capabilities'])->toBeArray()->toHaveKey('email');
    expect($manifest['capabilities']['email']['drivers'])->toContain('smtp', 'array');
    expect($manifest['env_keys'])->toContain('ETHEREAL_USERNAME', 'ETHEREAL_PASSWORD');
});
