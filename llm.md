# skim/mailer

`skim/mailer` is a SKIM extension that wraps Symfony Mailer behind a small facade.

## Install

```bash
composer require skim/mailer
php skim ext:install skim/mailer
```

The installer publishes `config/skim_mailer.php`. Set `MAIL_DRIVER` to `smtp`,
`mailgun`, `postmark`, `ses`, `sendgrid`, `log`, or `array`.

## Usage

```php
use Skim\Mailer\Mailer;
use Skim\Mailer\Mailable;

mailer::to('user@example.com')
    ->subject('Hello')
    ->view('emails/hello', ['name' => 'Ada'])
    ->send();

final class welcome_mail extends mailable {
    public function __construct(private object $user) {}

    public function build(): void {
        $this->to($this->user->email)
            ->subject('Welcome')
            ->view('emails/welcome', ['user' => $this->user]);
    }
}

mailer::send(new welcome_mail($user));
```

## Testing

```php
mailer::fake();
mailer::send(new welcome_mail($user));
mailer::assert_sent(welcome_mail::class);
mailer::assert_sent_to('user@example.com', welcome_mail::class);
mailer::assert_nothing_sent();
```

## Email preview with Ethereal

Set `ETHEREAL_USERNAME` and `ETHEREAL_PASSWORD`, then send a preview test email:

```bash
php skim mail:preview-test you@example.com
```

The message is sent to Ethereal's fake SMTP service and can be viewed at
`https://ethereal.email/messages`.
