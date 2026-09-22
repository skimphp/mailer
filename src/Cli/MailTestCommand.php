<?php declare(strict_types=1);

namespace Skim\Mailer\Cli;

use Skim\Cli\Command;
use Skim\Mailer\Mailer;

final class MailTestCommand extends Command {
    protected string $description = 'send a test email through skim/mailer';
    protected string $usage = '<email>';

    public function handle(): int {
        $to = (string) $this->arg(0, '');
        if ($to === '') {
            $this->error('Usage: php skim mail:test you@example.com');
            return 1;
        }

        Mailer::to($to)
            ->subject('SKIM mail test')
            ->html('<p>This is a test email from skim/mailer.</p>')
            ->send();

        $this->success("Sent test email to {$to}");
        return 0;
    }
}
