<?php declare(strict_types=1);

use Skim\Mailer\Mailable;
use Skim\Mailer\Mailer;
use Skim\Mailer\Transports\ArrayTransport;

final class TestMail extends Mailable {
    public function __construct(private readonly string $address) {}

    public function build(): void {
        $this->to($this->address)->subject('Test')->html('Test');
    }
}

beforeEach(fn() => Mailer::fake());

it('records sent mailable', function(): void {
    Mailer::send(new TestMail('user@example.com'));

    expect(ArrayTransport::sent())->toHaveCount(1);
    Mailer::assertSent(TestMail::class);
    Mailer::assertSentTo('user@example.com', TestMail::class);
});

it('passes assert_nothing_sent when queue empty', function(): void {
    expect(ArrayTransport::sent())->toBe([]);
    Mailer::assertNothingSent();
});
