<?php declare(strict_types=1);

namespace Skim\Mailer;

use Skim\View\View;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class Mailable {
    protected array $to = [];
    protected string $subject = '';
    protected ?string $template = null;
    protected array $data = [];
    protected array $attachments = [];
    protected ?string $html = null;
    private bool $built = false;

    public function to(string|array $address): static {
        foreach ($this->normalizeAddresses($address) as $item) {
            $this->to[] = $item;
        }

        return $this;
    }

    public function subject(string $subject): static {
        $this->subject = $subject;

        return $this;
    }

    public function view(string $template, array $data = []): static {
        $this->template = $template;
        $this->data = $data;

        return $this;
    }

    public function html(string $html): static {
        $this->html = $html;

        return $this;
    }

    public function attach(string $path, string $name): static {
        $this->attachments[] = ['path' => $path, 'name' => $name];

        return $this;
    }

    public function send(): void {
        Mailer::send($this);
    }

    public function build(): void {}

    public function toEmail(array $from): Email {
        $this->ensureBuilt();

        $email = (new Email())
            ->from(new Address($from['address'], $from['name']))
            ->subject($this->subject);

        foreach ($this->to as $recipient) {
            $email->addTo(new Address($recipient['address'], $recipient['name']));
        }

        if ($this->html !== null) {
            $email->html($this->html);
        } elseif ($this->template !== null) {
            $email->html(View::render($this->template, $this->data));
        } else {
            $email->text('');
        }

        foreach ($this->attachments as $attachment) {
            $email->attachFromPath($attachment['path'], $attachment['name']);
        }

        return $email;
    }

    public function recipientAddresses(): array {
        $this->ensureBuilt();

        return array_values(array_map(
            static fn(array $recipient): string => $recipient['address'],
            $this->to,
        ));
    }

    protected function ensureBuilt(): void {
        if ($this->built) {
            return;
        }

        $this->built = true;
        $this->build();
    }

    private function normalizeAddresses(string|array $address): array {
        if (is_string($address)) {
            return [['address' => $address, 'name' => '']];
        }

        $normalized = [];
        foreach ($address as $key => $value) {
            if (is_string($key)) {
                $normalized[] = ['address' => (string) $key, 'name' => (string) $value];
                continue;
            }

            if (is_array($value)) {
                $normalized[] = [
                    'address' => (string) ($value['address'] ?? $value['email'] ?? ''),
                    'name'    => (string) ($value['name'] ?? ''),
                ];
                continue;
            }

            $normalized[] = ['address' => (string) $value, 'name' => ''];
        }

        return array_values(array_filter(
            $normalized,
            static fn(array $item): bool => $item['address'] !== '',
        ));
    }
}
