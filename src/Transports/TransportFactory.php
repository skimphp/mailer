<?php declare(strict_types=1);

namespace Skim\Mailer\Transports;

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\TransportInterface;

final class TransportFactory {
    public static function make(?array $config = null): TransportInterface {
        $config ??= (array) config('skim_mailer', []);
        $driver = strtolower((string) ($config['driver'] ?? 'smtp'));

        return match ($driver) {
            'smtp'     => Transport::fromDsn(self::smtpDsn((array) ($config['smtp'] ?? []))),
            'mailgun'  => Transport::fromDsn(self::mailgunDsn((array) ($config['mailgun'] ?? []))),
            'postmark' => Transport::fromDsn(self::postmarkDsn((array) ($config['postmark'] ?? []))),
            'ses'      => Transport::fromDsn(self::sesDsn((array) ($config['ses'] ?? []))),
            'sendgrid' => Transport::fromDsn(self::sendgridDsn((array) ($config['sendgrid'] ?? []))),
            'log'      => new LogTransport((string) ($config['log']['path'] ?? storagePath('logs/mail.log'))),
            'array'    => new ArrayTransport(),
            default    => throw new \InvalidArgumentException("Unsupported mail driver: {$driver}"),
        };
    }

    private static function smtpDsn(array $smtp): string {
        $host = (string) ($smtp['host'] ?? 'localhost');
        $port = (int) ($smtp['port'] ?? 587);
        $encryption = strtolower((string) ($smtp['encryption'] ?? 'tls'));
        $scheme = in_array($encryption, ['ssl', 'smtps'], true) ? 'smtps' : 'smtp';
        $username = $smtp['username'] ?? null;
        $password = $smtp['password'] ?? null;
        $auth = '';

        if ($username !== null && $username !== '') {
            $auth = rawurlencode((string) $username) . ':' . rawurlencode((string) $password) . '@';
        }

        return "{$scheme}://{$auth}{$host}:{$port}";
    }

    private static function mailgunDsn(array $mailgun): string {
        $key = rawurlencode((string) ($mailgun['key'] ?? ''));
        $domain = rawurlencode((string) ($mailgun['domain'] ?? ''));

        return "mailgun+api://{$key}:{$domain}@default";
    }

    private static function postmarkDsn(array $postmark): string {
        $token = rawurlencode((string) ($postmark['token'] ?? ''));

        return "postmark+api://{$token}@default";
    }

    private static function sesDsn(array $ses): string {
        $key = rawurlencode((string) ($ses['key'] ?? ''));
        $secret = rawurlencode((string) ($ses['secret'] ?? ''));
        $region = rawurlencode((string) ($ses['region'] ?? 'us-east-1'));

        return "ses+api://{$key}:{$secret}@default?region={$region}";
    }

    private static function sendgridDsn(array $sendgrid): string {
        $key = rawurlencode((string) ($sendgrid['key'] ?? ''));

        return "sendgrid+api://{$key}@default";
    }
}
