<?php declare(strict_types=1);

namespace Skim\Mailer\Transports;

use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\RawMessage;

final class LogTransport implements TransportInterface {
    public function __construct(
        private readonly string $path,
    ) {}

    public function send(RawMessage $message, ?Envelope $envelope = null): ?SentMessage {
        $sent = new SentMessage($message, $envelope ?? Envelope::create($message));
        $dir = dirname($this->path);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents(
            $this->path,
            '[' . date(DATE_ATOM) . "]\n" . $message->toString() . "\n\n",
            FILE_APPEND | LOCK_EX,
        );

        return $sent;
    }

    public function __toString(): string {
        return 'Log://' . $this->path;
    }
}
