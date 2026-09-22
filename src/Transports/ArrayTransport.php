<?php declare(strict_types=1);

namespace Skim\Mailer\Transports;

use Skim\Mailer\Mailable;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;

final class ArrayTransport implements TransportInterface {
    private static array $sent = [];
    private static array $mailables = [];

    public function send(RawMessage $message, ?Envelope $envelope = null): ?SentMessage {
        $sent = new SentMessage($message, $envelope ?? Envelope::create($message));
        self::$sent[] = [
            'message' => $message,
            'sent'    => $sent,
        ];

        return $sent;
    }

    public function recordMailable(Mailable $mailable, Email $email): void {
        self::$mailables[] = $mailable;
        $last = array_key_last(self::$sent);
        if ($last !== null) {
            self::$sent[$last]['mailable'] = $mailable;
            self::$sent[$last]['email'] = $email;
        }
    }

    public static function sent(): array {
        return self::$sent;
    }

    public static function mailables(): array {
        return self::$mailables;
    }

    public static function clear(): void {
        self::$sent = [];
        self::$mailables = [];
    }

    public function __toString(): string {
        return 'array://';
    }
}
