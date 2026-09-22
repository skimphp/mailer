<?php declare(strict_types=1);

namespace Skim\Mailer\Cli;

use Skim\Cli\Command;
use Skim\Mailer\MailerService;

final class EtherealTestCommand extends Command {
    protected string $description = 'send a preview test email through Ethereal';
    protected string $usage = '<email>';

    public function handle(): int {
        $to = (string) $this->arg(0, '');
        if ($to === '') {
            $this->error('Usage: php skim mail:preview-test you@example.com');
            return 1;
        }

        $username = (string) env('ETHEREAL_USERNAME', '');
        $password = (string) env('ETHEREAL_PASSWORD', '');
        if ($username === '' || $password === '') {
            $this->error('Set ETHEREAL_USERNAME and ETHEREAL_PASSWORD before running mail:preview-test.');
            return 1;
        }

        $mailer = new MailerService(config: [
            'driver' => 'smtp',
            'from'   => [
                'address' => (string) env('ETHEREAL_FROM', $username),
                'name'    => (string) env('ETHEREAL_FROM_NAME', 'SKIM Preview'),
            ],
            'smtp'   => [
                'host'       => 'smtp.ethereal.email',
                'port'       => 587,
                'encryption' => 'tls',
                'username'   => $username,
                'password'   => $password,
            ],
        ]);

        $message = $mailer->to($to)
            ->subject('SKIM email preview test')
            ->html('<p>This is a preview test email from skim/mailer.</p>');

        $mailer->send($message);

        $this->success("Sent Ethereal preview test email to {$to}");
        $this->info('Open inbox: https://ethereal.email/messages');
        $this->info("Login: {$username}");

        return 0;
    }
}
