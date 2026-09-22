<?php declare(strict_types=1);

namespace Skim\Mailer;

use Skim\Core\App;
use Skim\Mailer\Transports\ArrayTransport;

final class Mailer {
    private static ?MailerService $fake = null;

    public static function send(Mailable $mailable): void {
        self::service()->send($mailable);
    }

    public static function to(string|array $address): Mailable {
        return self::service()->to($address);
    }

    public static function fake(): void {
        ArrayTransport::clear();
        self::$fake = new MailerService(new ArrayTransport());
    }

    public static function restore(): void {
        self::$fake = null;
        ArrayTransport::clear();
    }

    public static function reset(): void {
        self::restore();
    }

    public static function assertSent(string $mailable_class): void {
        foreach (ArrayTransport::mailables() as $mailable) {
            if ($mailable instanceof $mailable_class) {
                return;
            }
        }

        throw new \RuntimeException("Expected mailable {$mailable_class} was not sent.");
    }

    public static function assertSentTo(string $address, string $mailable_class): void {
        foreach (ArrayTransport::mailables() as $mailable) {
            if (!$mailable instanceof $mailable_class) {
                continue;
            }

            if (in_array(strtolower($address), array_map('strtolower', $mailable->recipientAddresses()), true)) {
                return;
            }
        }

        throw new \RuntimeException("Expected mailable {$mailable_class} was not sent to {$address}.");
    }

    public static function assertNothingSent(): void {
        if (ArrayTransport::sent() !== []) {
            throw new \RuntimeException('Expected no mail to be sent.');
        }
    }

    private static function service(): MailerService {
        if (self::$fake !== null) {
            return self::$fake;
        }

        return App::instance()->make(MailerService::class);
    }
}
