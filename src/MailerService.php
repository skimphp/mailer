<?php declare(strict_types=1);

namespace Skim\Mailer;

use Skim\Mailer\Transports\ArrayTransport;
use Skim\Mailer\Transports\TransportFactory;
use Symfony\Component\Mailer\Mailer as SymfonyMailer;
use Symfony\Component\Mailer\Transport\TransportInterface;

final class MailerService {
    private SymfonyMailer $mailer;
    private TransportInterface $transport;
    private array $config;

    public function __construct(?TransportInterface $transport = null, ?array $config = null) {
        $this->config = $config ?? (array) config('skim_mailer', []);
        $this->transport = $transport ?? TransportFactory::make($this->config);
        $this->mailer = new SymfonyMailer($this->transport);
    }

    public function send(Mailable $mailable): void {
        $email = $mailable->toEmail($this->from());
        $this->mailer->send($email);

        if ($this->transport instanceof ArrayTransport) {
            $this->transport->recordMailable($mailable, $email);
        }
    }

    public function to(string|array $address): Mailable {
        return (new mailable())->to($address);
    }

    public function transport(): TransportInterface {
        return $this->transport;
    }

    private function from(): array {
        $from = (array) ($this->config['from'] ?? []);

        return [
            'address' => (string) ($from['address'] ?? 'hello@example.com'),
            'name'    => (string) ($from['name'] ?? 'Example'),
        ];
    }
}
