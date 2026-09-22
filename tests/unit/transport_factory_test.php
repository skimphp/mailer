<?php declare(strict_types=1);

use Skim\Mailer\Transports\ArrayTransport;
use Skim\Mailer\Transports\TransportFactory;

it('builds array transport for test driver', function(): void {
    expect(TransportFactory::make(['driver' => 'array']))
        ->toBeInstanceOf(ArrayTransport::class);
});

it('throws for unknown driver', function(): void {
    expect(fn() => TransportFactory::make(['driver' => 'unknown']))
        ->toThrow(InvalidArgumentException::class);
});
