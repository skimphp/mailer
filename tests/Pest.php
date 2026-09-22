<?php declare(strict_types=1);

use Skim\Mailer\Mailer;

uses()->beforeEach(fn() => Mailer::reset())->in(__DIR__);
