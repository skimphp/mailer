# skim/mailer

`skim/mailer` wraps Symfony Mailer for SKIM applications, offering a clean, fluent interface to construct and dispatch emails via SMTP, Mailgun, Postmark, Sendgrid, or local arrays.

## Integration & Installation

To install the extension, require it in your SKIM application:

```bash
composer require skim/mailer
```

The extension is automatically detected and registered by the SKIM Kernel, activating the `email` capability.

## Configuration

Copy the default mailer configuration to your application's `config/skim_mailer.php` (if not already published) and define your SMTP or service credentials in `.env`:

```dotenv
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_FROM="app@example.com"
MAIL_FROM_NAME="SKIM Application"
```

---

## Code Examples & Usage

### 1. Defining a Mailable Class

Inherit from `Skim\Mailer\Mailable` and implement the `build()` method using fluent methods:

```php
namespace App\Mail;

use Skim\Mailer\Mailable;

class welcome_mail extends mailable {
    public function __construct(private string $name) {}

    public function build(): void {
        $this->to($this->recipient)
             ->subject('Welcome to SKIM!')
             ->html("<h1>Hello, {$this->name}!</h1><p>We are glad to have you aboard.</p>");
    }
}
```

### 2. Sending Emails

Retrieve the mailer service and dispatch mailables:

```php
use App\Mail\WelcomeMail;
use Skim\Mailer\Mailer;

// Senders can invoke via the facade/class helper:
mailer::send(new welcome_mail('Alice'));

// Or resolve the underlying mailer_service via DI:
$mailer = app('mailer');
$mailer->send(new welcome_mail('Bob'));
```

### 3. Faking & Asserting Sent Mail in Tests

Easily assert sent messages in your unit and feature tests:

```php
use App\Mail\WelcomeMail;
use Skim\Mailer\Mailer;

it('sends welcome email', function() {
    // Swap transport with a fake array recorder
    mailer::fake();

    // Perform application logic that sends email...
    mailer::send(new welcome_mail('Alice'));

    // Assert mail was sent out
    mailer::assert_sent(welcome_mail::class);
    mailer::assert_sent_to('alice@example.com', welcome_mail::class);
});
```

---

## Email preview with Ethereal

Use this when you want to send a real SMTP message, but preview it instantly in
Ethereal instead of delivering it to a real mailbox.

Ethereal SMTP settings are built into the `mail:preview-test` command:

- host: `smtp.ethereal.email`
- port: `587`
- security: `STARTTLS`

## 1. Create Ethereal credentials

Open [https://ethereal.email/login](https://ethereal.email/login).

If you do not have an account yet, use the account creation link on that page.
Ethereal will give you:

- email address
- password

These are test credentials. Do not commit them.

## 2. Add credentials to this dev package checkout

For the current local dev layout, put the values into:

```text
/Users/liquan/Documents/web/skim_extensions/mailer/.env.ethereal
```

Example:

```dotenv
ETHEREAL_USERNAME=your-ethereal-address@ethereal.email
ETHEREAL_PASSWORD=your-ethereal-password
ETHEREAL_FROM=your-ethereal-address@ethereal.email
ETHEREAL_FROM_NAME="SKIM Preview"
```

`ETHEREAL_FROM` and `ETHEREAL_FROM_NAME` are optional. If `ETHEREAL_FROM` is not
set, the command uses `ETHEREAL_USERNAME` as the sender address.

`.env.ethereal` is gitignored. Do not put these credentials in
`skim_framework/.env`; the framework repo only provides the Docker/PHP runtime
for this standalone package test.

## 3. Install package dependencies for local preview

From the mailer package:

```bash
cd /Users/liquan/Documents/web/skim_extensions/mailer
```

Then use the `skim_framework` Docker app container as the PHP/composer runtime:

```bash
docker compose \
  -f /Users/liquan/Documents/web/skim_framework/docker-compose.yml \
  run --rm -T \
  -v /Users/liquan/Documents/web/skim_extensions/mailer:/work \
  -v /Users/liquan/Documents/web/skim_framework:/skim_framework \
  -w /work \
  app sh -lc 'composer config repositories.skim_framework "{\"type\":\"path\",\"url\":\"/skim_framework\",\"options\":{\"versions\":{\"skim/framework\":\"1.0.0\"}}}" && composer install'
```

This writes `vendor/` and `composer.lock` inside
`/Users/liquan/Documents/web/skim_extensions/mailer`; both are gitignored.

## 4. Send a preview email

From the mailer package:

```bash
docker compose \
  -f /Users/liquan/Documents/web/skim_framework/docker-compose.yml \
  run --rm -T \
  -v /Users/liquan/Documents/web/skim_extensions/mailer:/work \
  -v /Users/liquan/Documents/web/skim_framework:/skim_framework \
  -w /work \
  app php bin/preview-test you@example.com
```

If you have local PHP/composer installed, the equivalent local command is:

```bash
php bin/preview-test you@example.com
```

The recipient can be any valid email address. Ethereal does not deliver the
message to that address; it stores the message in the Ethereal account used for
SMTP auth.

The installed extension command name is still:

```bash
php skim mail:preview-test you@example.com
```

Use that form only inside a real SKIM app where `skim/mailer` is installed and
registered as an extension. For this standalone dev package checkout, use
`php bin/preview-test`.

## 5. Open the preview

After a successful send, the command prints:

```text
Open inbox: https://ethereal.email/messages
Login: your-ethereal-address@ethereal.email
```

Open [https://ethereal.email/messages](https://ethereal.email/messages), log in
with the same Ethereal credentials, and open the latest message.

## Troubleshooting

If the command says credentials are missing, check that `ETHEREAL_USERNAME` and
`ETHEREAL_PASSWORD` are available inside the process:

```bash
docker compose \
  -f /Users/liquan/Documents/web/skim_framework/docker-compose.yml \
  run --rm -T \
  -v /Users/liquan/Documents/web/skim_extensions/mailer:/work \
  -w /work \
  app sh -lc 'grep ETHEREAL .env.ethereal'
```

If sending fails with an SMTP authentication error, create a new Ethereal account
and update the credentials in `.env.ethereal`.
